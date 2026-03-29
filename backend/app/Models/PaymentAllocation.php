<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



// ============================================
// PaymentAllocation Model
// ============================================
class PaymentAllocation extends Model
{
    protected $fillable = [
        'payment_id',
        'invoice_id',
        'allocated_amount_cents',
        'notes'
    ];

    protected $casts = [
        'allocated_amount_cents' => 'integer'
    ];

    protected $appends = [
        'allocated_amount'
    ];

    // Relationships
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accessors
    public function getAllocatedAmountAttribute(): float
    {
        return $this->allocated_amount_cents / 100;
    }

    // Mutators
    public function setAllocatedAmountAttribute($value)
    {
        $this->attributes['allocated_amount_cents'] = round($value * 100);
    }

    /**
     * Boot method to handle events
     */
    protected static function boot()
    {
        parent::boot();

        // After creating allocation, update invoice totals
        static::created(function ($allocation) {
            $allocation->invoice->calculateTotals();
        });

        // After deleting allocation, update invoice totals
        static::deleted(function ($allocation) {
            $allocation->invoice->calculateTotals();
        });
    }
}
