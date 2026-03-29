<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingActivity extends Model
{
    protected $fillable = [
        'booking_id',
        'event',
        'description',
        'old_values',
        'new_values',
        'performed_by_user_id',
        'ip_address'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    // Scopes
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    // Event Constants
    const EVENT_CREATED = 'created';
    const EVENT_UPDATED = 'updated';
    const EVENT_CHECKED_IN = 'checked-in';
    const EVENT_CHECKED_OUT = 'checked-out';
    const EVENT_CANCELLED = 'cancelled';
    const EVENT_NO_SHOW = 'no-show';
    const EVENT_CHARGE_ADDED = 'charge-added';
    const EVENT_CHARGE_REMOVED = 'charge-removed';
    const EVENT_PAYMENT_ADDED = 'payment-added';
    const EVENT_PAYMENT_REFUNDED = 'payment-refunded';
    const EVENT_ROOM_CHANGED = 'room-changed';
    const EVENT_GUEST_UPDATED = 'guest-updated';

    // Helper method to format activity for display
    public function getFormattedActivityAttribute(): string
    {
        $user = $this->performedBy ? $this->performedBy->name : 'System';
        $time = $this->created_at->diffForHumans();
        
        return "{$user} {$this->description} ({$time})";
    }
}
