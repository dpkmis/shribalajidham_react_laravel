<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'invoice_number',
        'booking_id',
        'guest_id',
        'status',
        'type',
        'issue_date',
        'due_date',
        'paid_date',
        'subtotal_cents',
        'discount_cents',
        'tax_cents',
        'total_cents',
        'paid_cents',
        'balance_cents',
        'currency',
        'tax_rate',
        'discount_percentage',
        'notes',
        'terms_and_conditions',
        'payment_terms',
        'billing_address',
        'billing_gstin',
        'created_by_user_id',
        'updated_by_user_id',
        'cancelled_by_user_id',
        'cancelled_at',
        'cancellation_reason',
        'meta'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'cancelled_at' => 'datetime',
        'subtotal_cents' => 'integer',
        'discount_cents' => 'integer',
        'tax_cents' => 'integer',
        'total_cents' => 'integer',
        'paid_cents' => 'integer',
        'balance_cents' => 'integer',
        'tax_rate' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'meta' => 'array'
    ];

    protected $appends = [
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid',
        'balance',
        'is_overdue',
        'days_overdue'
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    // Accessors for decimal amounts
    public function getSubtotalAttribute(): float
    {
        return $this->subtotal_cents / 100;
    }

    public function getDiscountAttribute(): float
    {
        return $this->discount_cents / 100;
    }

    public function getTaxAttribute(): float
    {
        return $this->tax_cents / 100;
    }

    public function getTotalAttribute(): float
    {
        return $this->total_cents / 100;
    }

    public function getPaidAttribute(): float
    {
        return $this->paid_cents / 100;
    }

    public function getBalanceAttribute(): float
    {
        return $this->balance_cents / 100;
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'paid' || !$this->due_date) {
            return false;
        }
        return $this->due_date->isPast();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return $this->due_date->diffInDays(now());
    }

    // Mutators to handle decimal to cents conversion
    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal_cents'] = round($value * 100);
    }

    public function setDiscountAttribute($value)
    {
        $this->attributes['discount_cents'] = round($value * 100);
    }

    public function setTaxAttribute($value)
    {
        $this->attributes['tax_cents'] = round($value * 100);
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total_cents'] = round($value * 100);
    }

    public function setPaidAttribute($value)
    {
        $this->attributes['paid_cents'] = round($value * 100);
    }

    public function setBalanceAttribute($value)
    {
        $this->attributes['balance_cents'] = round($value * 100);
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now());
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'pending', 'partially_paid', 'overdue']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeForGuest($query, $guestId)
    {
        return $query->where('guest_id', $guestId);
    }

    // Business Logic Methods

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber($propertyId): string
    {
        $property = Property::find($propertyId);
        $prefix = $property->invoice_prefix ?? 'INV';
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = self::where('property_id', $propertyId)
            ->where('invoice_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Calculate totals from line items
     */
    public function calculateTotals(): void
    {
        $subtotal = 0;
        $tax = 0;
        $discount = 0;

        foreach ($this->lineItems as $item) {
            $subtotal += $item->subtotal_cents;
            $tax += $item->tax_cents;
            $discount += $item->discount_cents;
        }

        $this->subtotal_cents = $subtotal;
        $this->discount_cents = $discount;
        $this->tax_cents = $tax;
        $this->total_cents = $subtotal - $discount + $tax;
        
        // Calculate paid amount from allocations
        $paidAmount = $this->paymentAllocations()->sum('allocated_amount_cents');
        $this->paid_cents = $paidAmount;
        $this->balance_cents = $this->total_cents - $paidAmount;
        
        // Update status based on payment
        $this->updatePaymentStatus();
        
        $this->saveQuietly(); // Save without triggering events
    }

    /**
     * Update payment status based on paid amount
     */
    public function updatePaymentStatus(): void
    {
        if ($this->status === 'cancelled' || $this->status === 'refunded') {
            return; // Don't change status for cancelled/refunded
        }

        if ($this->balance_cents <= 0) {
            $this->status = 'paid';
            $this->paid_date = now();
        } elseif ($this->paid_cents > 0) {
            $this->status = 'partially_paid';
        } elseif ($this->due_date && $this->due_date->isPast()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }
    }

    /**
     * Add line item to invoice
     */
    public function addLineItem(array $data): InvoiceLineItem
    {
        $item = $this->lineItems()->create($data);
        $this->calculateTotals();
        return $item;
    }

    /**
     * Add payment to invoice
     */
    public function addPayment(Payment $payment, ?float $amount = null): PaymentAllocation
    {
        $amountCents = $amount ? (int)($amount * 100) : min($payment->amount_cents, $this->balance_cents);
        
        $allocation = PaymentAllocation::create([
            'payment_id' => $payment->id,
            'invoice_id' => $this->id,
            'allocated_amount_cents' => $amountCents
        ]);
        
        $this->calculateTotals();
        
        return $allocation;
    }

    /**
     * Cancel invoice
     */
    public function cancel(string $reason, ?int $userId = null): bool
    {
        if ($this->status === 'paid') {
            throw new \Exception('Cannot cancel a paid invoice. Create a credit note instead.');
        }

        if ($this->paid_cents > 0) {
            throw new \Exception('Cannot cancel an invoice with payments. Refund payments first.');
        }

        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by_user_id' => $userId ?? auth()->id(),
            'cancellation_reason' => $reason
        ]);

        return true;
    }

    /**
     * Mark invoice as sent
     */
    public function markAsSent(): void
    {
        if ($this->status === 'draft') {
            $this->update(['status' => 'pending']);
        }
    }

    /**
     * Apply discount to invoice
     */
    public function applyDiscount(float $percentage): void
    {
        $this->discount_percentage = $percentage;
        $this->save();
        
        // Recalculate line items with discount
        foreach ($this->lineItems as $item) {
            $item->discount_percentage = $percentage;
            $item->calculateTotals();
            $item->save();
        }
        
        $this->calculateTotals();
    }

    /**
     * Generate PDF invoice
     */
    public function generatePDF(): string
    {
        // TODO: Implement PDF generation using DOMPDF or similar
        // Return path to generated PDF
        return storage_path("invoices/invoice-{$this->invoice_number}.pdf");
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(): bool
    {
        if (!$this->guest || !$this->guest->email) {
            throw new \Exception('Guest email not found');
        }

        // TODO: Implement email sending
        // Mail::to($this->guest->email)->send(new InvoiceMail($this));
        
        return true;
    }

    /**
     * Create from booking
     */
    public static function createFromBooking(Booking $booking): self
    {
        $invoice = self::create([
            'property_id' => $booking->property_id,
            'invoice_number' => self::generateInvoiceNumber($booking->property_id),
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'status' => 'draft',
            'type' => 'booking',
            'issue_date' => now(),
            'due_date' => $booking->checkout_date,
            'currency' => $booking->currency ?? 'INR',
            'billing_address' => $booking->guest?->full_address,
            'created_by_user_id' => auth()->id()
        ]);

        // Add room charges as line items
        foreach ($booking->bookingRooms as $bookingRoom) {
            $invoice->addLineItem([
                'item_type' => 'room',
                'reference_type' => 'App\Models\BookingRoom',
                'reference_id' => $bookingRoom->id,
                'description' => "{$bookingRoom->roomType->name} - {$bookingRoom->room->room_number} ({$booking->nights} nights)",
                'quantity' => $booking->nights,
                'unit_price_cents' => $bookingRoom->rate_per_night_cents,
                'service_date' => $bookingRoom->checkin_date
            ]);
        }

        // Add additional charges
        foreach ($booking->charges as $charge) {
            $invoice->addLineItem([
                'item_type' => 'service',
                'reference_type' => 'App\Models\BookingCharge',
                'reference_id' => $charge->id,
                'description' => $charge->description,
                'quantity' => $charge->quantity,
                'unit_price_cents' => $charge->amount_cents,
                'service_date' => $charge->charge_date
            ]);
        }

        return $invoice;
    }

    /**
     * Check if invoice can be edited
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    /**
     * Check if invoice can be deleted
     */
    public function canDelete(): bool
    {
        return $this->status === 'draft' && $this->paid_cents == 0;
    }
}