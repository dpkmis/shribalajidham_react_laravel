<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\PaymentAllocation;
use App\Models\Refund;
use App\Models\User;


class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'payment_reference',
        'invoice_id',
        'booking_id',
        'guest_id',
        'amount_cents',
        'currency',
        'type',
        'method',
        'status',
        'transaction_id',
        'gateway',
        'gateway_response',
        'cheque_number',
        'cheque_date',
        'bank_name',
        'card_last_four',
        'card_brand',
        'paid_at',
        'cleared_at',
        'refunded_at',
        'remarks',
        'internal_notes',
        'received_by_user_id',
        'processed_by_user_id',
        'refunded_by_user_id',
        'meta'
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'gateway_response' => 'array',
        'cheque_date' => 'date',
        'paid_at' => 'datetime',
        'cleared_at' => 'datetime',
        'refunded_at' => 'datetime',
        'meta' => 'array'
    ];

    protected $appends = [
        'amount',
        'unallocated_amount',
        'is_fully_allocated'
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
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

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by_user_id');
    }

    // Accessors
    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    public function getUnallocatedAmountAttribute(): float
    {
        $allocated = $this->allocations()->sum('allocated_amount_cents');
        return ($this->amount_cents - $allocated) / 100;
    }

    public function getIsFullyAllocatedAttribute(): bool
    {
        return $this->unallocated_amount <= 0;
    }

    // Mutators
    public function setAmountAttribute($value)
    {
        $this->attributes['amount_cents'] = round($value * 100);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeForGuest($query, $guestId)
    {
        return $query->where('guest_id', $guestId);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('paid_at', [$startDate, $endDate]);
    }

    public function scopeUnallocated($query)
    {
        return $query->whereHas('allocations', function($q) {
            // Payments where allocated amount is less than payment amount
        }, '<', 1);
    }

    // Business Logic Methods

    /**
     * Generate unique payment reference
     */
    public static function generatePaymentReference($propertyId): string
    {
        $property = Property::find($propertyId);
        $prefix = $property->payment_prefix ?? 'PAY';
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = self::where('property_id', $propertyId)
            ->where('payment_reference', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_reference, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Allocate payment to invoice(s)
     */
    public function allocateToInvoice(Invoice $invoice, ?float $amount = null): PaymentAllocation
    {
        $remainingAmount = $this->unallocated_amount * 100; // Convert to cents
        
        if ($remainingAmount <= 0) {
            throw new \Exception('Payment is fully allocated');
        }

        $allocateAmount = $amount ? (int)($amount * 100) : min($remainingAmount, $invoice->balance_cents);
        
        if ($allocateAmount > $remainingAmount) {
            throw new \Exception('Allocation amount exceeds unallocated payment amount');
        }

        if ($allocateAmount > $invoice->balance_cents) {
            throw new \Exception('Allocation amount exceeds invoice balance');
        }

        $allocation = PaymentAllocation::create([
            'payment_id' => $this->id,
            'invoice_id' => $invoice->id,
            'allocated_amount_cents' => $allocateAmount
        ]);

        $invoice->calculateTotals();

        return $allocation;
    }

    /**
     * Auto-allocate payment to outstanding invoices
     */
    public function autoAllocate(): array
    {
        $allocations = [];
        $remainingAmount = $this->unallocated_amount * 100;

        if ($remainingAmount <= 0) {
            return $allocations;
        }

        // Get outstanding invoices for this guest/booking
        $query = Invoice::where('property_id', $this->property_id)
            ->where('balance_cents', '>', 0)
            ->orderBy('due_date', 'asc')
            ->orderBy('issue_date', 'asc');

        if ($this->guest_id) {
            $query->where('guest_id', $this->guest_id);
        } elseif ($this->booking_id) {
            $query->where('booking_id', $this->booking_id);
        }

        $invoices = $query->get();

        foreach ($invoices as $invoice) {
            if ($remainingAmount <= 0) {
                break;
            }

            $allocateAmount = min($remainingAmount, $invoice->balance_cents);
            
            $allocation = $this->allocateToInvoice($invoice, $allocateAmount / 100);
            $allocations[] = $allocation;
            
            $remainingAmount -= $allocateAmount;
        }

        return $allocations;
    }

    /**
     * Process payment (for pending payments)
     */
    public function process(?int $userId = null): bool
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending payments can be processed');
        }

        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'processed_by_user_id' => $userId ?? auth()->id()
        ]);

        // Auto-allocate if linked to booking/guest
        if ($this->booking_id || $this->guest_id) {
            $this->autoAllocate();
        }

        return true;
    }

    /**
     * Mark cheque as cleared
     */
    public function markCleared(?int $userId = null): bool
    {
        if ($this->method !== 'cheque') {
            throw new \Exception('Only cheque payments can be marked as cleared');
        }

        if ($this->status !== 'completed') {
            throw new \Exception('Payment must be completed first');
        }

        $this->update([
            'cleared_at' => now(),
            'processed_by_user_id' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Initiate refund
     */
    public function initiateRefund(float $amount, string $reason, array $data = []): Refund
    {
        if ($this->status !== 'completed') {
            throw new \Exception('Only completed payments can be refunded');
        }

        if ($this->refund) {
            throw new \Exception('Payment already has a refund initiated');
        }

        $amountCents = (int)($amount * 100);
        
        if ($amountCents > $this->amount_cents) {
            throw new \Exception('Refund amount cannot exceed payment amount');
        }

        $refund = Refund::create(array_merge([
            'property_id' => $this->property_id,
            'refund_reference' => Refund::generateRefundReference($this->property_id),
            'payment_id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'booking_id' => $this->booking_id,
            'guest_id' => $this->guest_id,
            'amount_cents' => $amountCents,
            'currency' => $this->currency,
            'method' => $data['method'] ?? 'original_method',
            'reason' => $reason,
            'reason_description' => $data['reason_description'] ?? null,
            'status' => 'pending',
            'initiated_by_user_id' => auth()->id()
        ], $data));

        return $refund;
    }

    /**
     * Complete refund
     */
    public function completeRefund(): bool
    {
        if (!$this->refund) {
            throw new \Exception('No refund found for this payment');
        }

        if ($this->refund->status !== 'pending') {
            throw new \Exception('Refund is not in pending status');
        }

        $this->refund->complete();

        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'refunded_by_user_id' => auth()->id()
        ]);

        // Remove allocations
        foreach ($this->allocations as $allocation) {
            $invoice = $allocation->invoice;
            $allocation->delete();
            $invoice->calculateTotals();
        }

        return true;
    }

    /**
     * Void/Cancel payment
     */
    public function void(string $reason, ?int $userId = null): bool
    {
        if (!in_array($this->status, ['pending', 'completed'])) {
            throw new \Exception('Cannot void payment in current status');
        }

        if ($this->allocations()->count() > 0) {
            throw new \Exception('Cannot void payment with allocations. Remove allocations first.');
        }

        $this->update([
            'status' => 'cancelled',
            'internal_notes' => ($this->internal_notes ?? '') . "\nVoided: {$reason}",
            'processed_by_user_id' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Get payment receipt data
     */
    public function getReceiptData(): array
    {
        return [
            'payment_reference' => $this->payment_reference,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'method' => $this->method,
            'paid_at' => $this->paid_at,
            'guest' => $this->guest,
            'booking' => $this->booking,
            'allocations' => $this->allocations->map(function($allocation) {
                return [
                    'invoice_number' => $allocation->invoice->invoice_number,
                    'amount' => $allocation->allocated_amount_cents / 100
                ];
            })
        ];
    }

    /**
     * Generate PDF receipt
     */
    public function generateReceipt(): string
    {
        // TODO: Implement PDF generation
        return storage_path("receipts/receipt-{$this->payment_reference}.pdf");
    }

    /**
     * Send receipt via email
     */
    public function sendReceipt(): bool
    {
        if (!$this->guest || !$this->guest->email) {
            throw new \Exception('Guest email not found');
        }

        // TODO: Implement email sending
        // Mail::to($this->guest->email)->send(new PaymentReceiptMail($this));
        
        return true;
    }

    /**
     * Check if payment can be edited
     */
    public function canEdit(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment can be deleted
     */
    public function canDelete(): bool
    {
        return $this->status === 'pending' && $this->allocations()->count() === 0;
    }

    /**
     * Check if payment can be refunded
     */
    public function canRefund(): bool
    {
        return $this->status === 'completed' && !$this->refund;
    }
}