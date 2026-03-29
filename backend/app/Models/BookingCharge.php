<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCharge extends Model
{
    protected $fillable = [
        'booking_id',
        'booking_room_id',
        'type',
        'description',
        'amount_cents',
        'quantity',
        'tax_rate_id',
        'tax_percentage',
        'tax_amount_cents',
        'is_refundable',
        'is_paid',
        'charge_date',
        'created_by_user_id'
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'quantity' => 'integer',
        'tax_percentage' => 'decimal:2',
        'tax_amount_cents' => 'integer',
        'is_refundable' => 'boolean',
        'is_paid' => 'boolean',
        'charge_date' => 'date'
    ];

    protected $appends = ['amount', 'total_amount'];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingRoom(): BelongsTo
    {
        return $this->belongsTo(BookingRoom::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Accessors
    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    public function getTotalAmountAttribute(): float
    {
        return ($this->amount_cents * $this->quantity) / 100;
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Charge Type Constants
    const TYPE_ROOM_CHARGE = 'room-charge';
    const TYPE_TAX = 'tax';
    const TYPE_SERVICE_CHARGE = 'service-charge';
    const TYPE_FOOD_BEVERAGE = 'food-beverage';
    const TYPE_LAUNDRY = 'laundry';
    const TYPE_MINIBAR = 'minibar';
    const TYPE_SPA = 'spa';
    const TYPE_TRANSPORTATION = 'transportation';
    const TYPE_EXTRA_BED = 'extra-bed';
    const TYPE_EARLY_CHECKIN = 'early-checkin';
    const TYPE_LATE_CHECKOUT = 'late-checkout';
    const TYPE_PET_CHARGE = 'pet-charge';
    const TYPE_PARKING = 'parking';
    const TYPE_DAMAGE = 'damage';
    const TYPE_OTHER = 'other';
}
