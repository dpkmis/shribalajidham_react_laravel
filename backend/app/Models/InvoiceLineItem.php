<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

// ============================================
// InvoiceLineItem Model
// ============================================
class InvoiceLineItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_type',
        'reference_id',
        'reference_type',
        'description',
        'quantity',
        'unit_price_cents',
        'subtotal_cents',
        'tax_rate',
        'tax_cents',
        'total_cents',
        'discount_percentage',
        'discount_cents',
        'service_date',
        'sort_order',
        'meta'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price_cents' => 'integer',
        'subtotal_cents' => 'integer',
        'tax_cents' => 'integer',
        'total_cents' => 'integer',
        'discount_cents' => 'integer',
        'tax_rate' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'service_date' => 'date',
        'sort_order' => 'integer',
        'meta' => 'array'
    ];

    protected $appends = [
        'unit_price',
        'subtotal',
        'tax',
        'total',
        'discount'
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessors
    public function getUnitPriceAttribute(): float
    {
        return $this->unit_price_cents / 100;
    }

    public function getSubtotalAttribute(): float
    {
        return $this->subtotal_cents / 100;
    }

    public function getTaxAttribute(): float
    {
        return $this->tax_cents / 100;
    }

    public function getTotalAttribute(): float
    {
        return $this->total_cents / 100;
    }

    public function getDiscountAttribute(): float
    {
        return $this->discount_cents / 100;
    }

    // Mutators
    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price_cents'] = round($value * 100);
    }

    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal_cents'] = round($value * 100);
    }

    public function setTaxAttribute($value)
    {
        $this->attributes['tax_cents'] = round($value * 100);
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total_cents'] = round($value * 100);
    }

    public function setDiscountAttribute($value)
    {
        $this->attributes['discount_cents'] = round($value * 100);
    }

    // Business Logic

    /**
     * Calculate totals for line item
     */
    public function calculateTotals(): void
    {
        // Calculate subtotal
        $this->subtotal_cents = $this->unit_price_cents * $this->quantity;
        
        // Calculate discount
        if ($this->discount_percentage > 0) {
            $this->discount_cents = round(($this->subtotal_cents * $this->discount_percentage) / 100);
        }
        
        // Amount after discount
        $amountAfterDiscount = $this->subtotal_cents - $this->discount_cents;
        
        // Calculate tax
        if ($this->tax_rate > 0) {
            $this->tax_cents = round(($amountAfterDiscount * $this->tax_rate) / 100);
        }
        
        // Calculate total
        $this->total_cents = $amountAfterDiscount + $this->tax_cents;
    }

    /**
     * Override save to auto-calculate totals
     */
    public function save(array $options = [])
    {
        if (!isset($options['skip_calculate'])) {
            $this->calculateTotals();
        }
        
        return parent::save($options);
    }

    /**
     * Apply discount to this line item
     */
    public function applyDiscount(float $percentage): void
    {
        $this->discount_percentage = $percentage;
        $this->calculateTotals();
        $this->save();
    }
}