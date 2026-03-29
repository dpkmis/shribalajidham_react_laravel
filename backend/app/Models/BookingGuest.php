<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuest extends Model
{
    protected $fillable = [
        'booking_id',
        'booking_room_id',
        'guest_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'dob',
        'guest_type',
        'id_type',
        'id_number',
        'is_primary'
    ];

    protected $casts = [
        'dob' => 'date',
        'is_primary' => 'boolean'
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingRoom(): BelongsTo
    {
        return $this->belongsTo(BookingRoom::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    // Accessor
    public function getFullNameAttribute(): string
    {
        if ($this->guest) {
            return $this->guest->full_name;
        }
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
