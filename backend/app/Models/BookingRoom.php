<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRoom extends Model
{
    protected $fillable = [
        'booking_id',
        'room_id',
        'room_type_id',
        'rate_plan_id',
        'checkin_date',
        'checkout_date',
        'rate_per_night_cents',
        'total_rate_cents',
        'discount_cents',
        'final_rate_cents',
        'status',
        'adults',
        'children',
        'meta'
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'rate_per_night_cents' => 'integer',
        'total_rate_cents' => 'integer',
        'discount_cents' => 'integer',
        'final_rate_cents' => 'integer',
        'adults' => 'integer',
        'children' => 'integer',
        'meta' => 'array'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
