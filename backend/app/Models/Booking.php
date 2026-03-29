<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ulid',
        'property_id',
        'guest_id',
        'booking_reference',
        'confirmation_number',
        'status',
        'source',
        'checkin_date',
        'checkout_date',
        'actual_checkin_at',
        'actual_checkout_at',
        'number_of_adults',
        'number_of_children',
        'number_of_infants',
        'currency',
        'room_charges_cents',
        'tax_amount_cents',
        'discount_amount_cents',
        'additional_charges_cents',
        'total_amount_cents',
        'paid_amount_cents',
        'balance_amount_cents',
        'payment_status',
        'special_requests',
        'arrival_time',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'company_id',
        'travel_agent_id',
        'agent_commission_percent',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'actual_checkin_at' => 'datetime',
        'actual_checkout_at' => 'datetime',
        'number_of_adults' => 'integer',
        'number_of_children' => 'integer',
        'number_of_infants' => 'integer',
        'room_charges_cents' => 'integer',
        'tax_amount_cents' => 'integer',
        'discount_amount_cents' => 'integer',
        'additional_charges_cents' => 'integer',
        'total_amount_cents' => 'integer',
        'paid_amount_cents' => 'integer',
        'balance_amount_cents' => 'integer',
        'cancelled_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $appends = [
        'nights',
        'total_amount',
        'balance_amount',
        'paid_amount',
        'is_checked_in',
        'is_checked_out',
        'payment_progress'
    ];

    // Boot method
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->ulid)) {
                $booking->ulid = (string) Str::ulid();
            }
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = self::generateBookingReference();
            }
        });
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function bookingGuests(): HasMany
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(BookingCharge::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(BookingActivity::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Accessors
    public function getNightsAttribute(): int
    {
        return $this->checkin_date->diffInDays($this->checkout_date);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->total_amount_cents / 100;
    }

    public function getBalanceAmountAttribute(): float
    {
        return $this->balance_amount_cents / 100;
    }

    public function getPaidAmountAttribute(): float
    {
        return $this->paid_amount_cents / 100;
    }

    public function getIsCheckedInAttribute(): bool
    {
        return $this->status === 'checked-in';
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return $this->status === 'checked-out';
    }

    public function getPaymentProgressAttribute(): int
    {
        if ($this->total_amount_cents <= 0) return 0;
        return (int) (($this->paid_amount_cents / $this->total_amount_cents) * 100);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'no-show']);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'confirmed')
            ->where('checkin_date', '>=', today());
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked-in');
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked-out');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('checkin_date', [$startDate, $endDate])
              ->orWhereBetween('checkout_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('checkin_date', '<=', $startDate)
                     ->where('checkout_date', '>=', $endDate);
              });
        });
    }

    // Helper Methods
    public static function generateBookingReference(): string
    {
        return 'BKG-' . strtoupper(Str::random(8));
    }

    public function calculateTotals(): void
    {
        // Calculate room charges
        $roomCharges = $this->bookingRooms()->sum('final_rate_cents');
        
        // Calculate additional charges
        $additionalCharges = $this->charges()
            ->whereNotIn('type', ['room-charge', 'tax'])
            ->sum('amount_cents');
        
        // Calculate tax
        $taxCharges = $this->charges()
            ->where('type', 'tax')
            ->sum('amount_cents');
        
        // Calculate total paid
        $paidAmount = $this->payments()
            ->where('status', 'completed')
            ->where('type', 'payment')
            ->sum('amount_cents');
        
        $total = $roomCharges + $additionalCharges + $taxCharges;
        
        $this->update([
            'room_charges_cents' => $roomCharges,
            'additional_charges_cents' => $additionalCharges,
            'tax_amount_cents' => $taxCharges,
            'total_amount_cents' => $total,
            'paid_amount_cents' => $paidAmount,
            'balance_amount_cents' => $total - $paidAmount
        ]);
        
        // Update payment status
        $this->updatePaymentStatus();
    }

    public function updatePaymentStatus(): void
    {
        if ($this->balance_amount_cents <= 0) {
            $this->update(['payment_status' => 'paid']);
        } elseif ($this->paid_amount_cents > 0) {
            $this->update(['payment_status' => 'partially-paid']);
        } else {
            $this->update(['payment_status' => 'unpaid']);
        }
    }

    public function checkIn(): void
    {
        $this->update([
            'status' => 'checked-in',
            'actual_checkin_at' => now()
        ]);
        
        // Mark rooms as occupied
        $this->bookingRooms()->each(function ($bookingRoom) {
            $bookingRoom->room?->markAsOccupied();
        });
        
        $this->logActivity('checked-in', 'Guest checked in');
    }

    public function checkOut(): void
    {
        $this->update([
            'status' => 'checked-out',
            'actual_checkout_at' => now()
        ]);
        
        // Mark rooms as available
        $this->bookingRooms()->each(function ($bookingRoom) {
            $bookingRoom->room?->markAsAvailable();
        });
        
        $this->logActivity('checked-out', 'Guest checked out');
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now()
        ]);
        
        // Free up rooms
        $this->bookingRooms()->update(['status' => 'cancelled']);
        
        $this->logActivity('cancelled', 'Booking cancelled: ' . $reason);
    }

    public function logActivity(string $event, string $description, array $oldValues = null, array $newValues = null): void
    {
        $this->activities()->create([
            'event' => $event,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'performed_by_user_id' => auth()->id(),
            'ip_address' => request()->ip()
        ]);
    }

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked-in';
    const STATUS_CHECKED_OUT = 'checked-out';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no-show';
}