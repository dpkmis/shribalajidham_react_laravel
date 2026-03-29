<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Refund extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'property_id',
        'refund_reference',
        'payment_id',
        'invoice_id',
        'booking_id',
        'guest_id',
        'amount_cents',
        'currency',
        'method',
        'reason',
        'status',
        'transaction_id',
        'gateway_response',
        'reason_description',
        'internal_notes',
        'processed_at',
        'completed_at',
        'initiated_by_user_id',
        'processed_by_user_id',
        'approved_by_user_id',
        'meta'
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'meta' => 'array'
    ];

    protected $appends = [
        'amount'
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by_user_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    // Accessors
    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    // Mutators
    public function setAmountAttribute($value)
    {
        $this->attributes['amount_cents'] = round($value * 100);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Business Logic

    /**
     * Generate unique refund reference
     */
    public static function generateRefundReference($propertyId): string
    {
        $property = Property::find($propertyId);
        $prefix = $property->refund_prefix ?? 'REF';
        $year = date('Y');
        $month = date('m');
        
        $lastRefund = self::where('property_id', $propertyId)
            ->where('refund_reference', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastRefund) {
            $lastNumber = (int) substr($lastRefund->refund_reference, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Approve refund
     */
    public function approve(?int $userId = null): bool
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending refunds can be approved');
        }

        $this->update([
            'status' => 'processing',
            'approved_by_user_id' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Process refund
     */
    public function process(?int $userId = null): bool
    {
        if (!in_array($this->status, ['pending', 'processing'])) {
            throw new \Exception('Invalid refund status for processing');
        }

        // Here you would integrate with payment gateway to process refund
        // For now, we'll just mark it as processing
        
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
            'processed_by_user_id' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Complete refund
     */
    public function complete(?array $gatewayResponse = null): bool
    {
        if ($this->status === 'completed') {
            throw new \Exception('Refund is already completed');
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'gateway_response' => $gatewayResponse
        ]);

        // Update payment status
        $this->payment->update([
            'status' => 'refunded',
            'refunded_at' => now()
        ]);

        // If invoice exists, create credit note
        if ($this->invoice) {
            $this->createCreditNote();
        }

        return true;
    }

    /**
     * Fail refund
     */
    public function fail(string $reason, ?array $gatewayResponse = null): bool
    {
        $this->update([
            'status' => 'failed',
            'internal_notes' => ($this->internal_notes ?? '') . "\nFailed: {$reason}",
            'gateway_response' => $gatewayResponse
        ]);

        return true;
    }

    /**
     * Cancel refund
     */
    public function cancel(string $reason, ?int $userId = null): bool
    {
        if (!in_array($this->status, ['pending', 'processing'])) {
            throw new \Exception('Cannot cancel refund in current status');
        }

        $this->update([
            'status' => 'cancelled',
            'internal_notes' => ($this->internal_notes ?? '') . "\nCancelled: {$reason}",
            'processed_by_user_id' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Create credit note for refund
     */
    protected function createCreditNote(): Invoice
    {
        $creditNote = Invoice::create([
            'property_id' => $this->property_id,
            'invoice_number' => 'CN-' . Invoice::generateInvoiceNumber($this->property_id),
            'booking_id' => $this->booking_id,
            'guest_id' => $this->guest_id,
            'status' => 'paid',
            'type' => 'credit_note',
            'issue_date' => now(),
            'total_cents' => -$this->amount_cents, // Negative amount for credit
            'paid_cents' => -$this->amount_cents,
            'balance_cents' => 0,
            'currency' => $this->currency,
            'notes' => "Credit Note for refund: {$this->refund_reference}",
            'created_by_user_id' => auth()->id()
        ]);

        $creditNote->addLineItem([
            'item_type' => 'refund',
            'reference_type' => 'App\Models\Refund',
            'reference_id' => $this->id,
            'description' => "Refund - {$this->reason_description}",
            'quantity' => 1,
            'unit_price_cents' => -$this->amount_cents,
            'service_date' => now()
        ]);

        return $creditNote;
    }

    /**
     * Check if refund can be cancelled
     */
    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
