<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Guest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ulid',
        'property_id',
        'first_name',
        'last_name',
        'middle_name',
        'title',
        'gender',
        'dob',
        'nationality',
        'email',
        'phone',
        'alternate_phone',
        'whatsapp',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'postal_code',
        'company_name',
        'company_designation',
        'gstin',
        'id_type',
        'id_number',
        'id_expiry_date',
        'id_document_path',
        'preferred_language',
        'meal_preference',
        'special_requests',
        'allergies',
        'guest_type',
        'is_blacklisted',
        'blacklist_reason',
        'is_vip',
        'loyalty_points',
        'marketing_consent',
        'sms_consent',
        'email_consent',
        'photo_path',
        'created_by_user_id',
        'updated_by_user_id',
        'meta'
    ];

    protected $casts = [
        'dob' => 'date',
        'id_expiry_date' => 'date',
        'is_blacklisted' => 'boolean',
        'is_vip' => 'boolean',
        'loyalty_points' => 'decimal:2',
        'marketing_consent' => 'boolean',
        'sms_consent' => 'boolean',
        'email_consent' => 'boolean',
        'meta' => 'array',
        'deleted_at' => 'datetime'
    ];

    protected $appends = ['full_name', 'age', 'complete_address'];

    // Boot method
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($guest) {
            if (empty($guest->ulid)) {
                $guest->ulid = (string) Str::ulid();
            }
        });
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingGuests(): HasMany
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->dob ? $this->dob->age : null;
    }

    public function getCompleteAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);
        
        return implode(', ', $parts);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_blacklisted', false);
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('is_blacklisted', true);
    }

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function getTotalBookings(): int
    {
        return $this->bookings()->count();
    }

    public function getTotalSpent(): float
    {
        return $this->bookings()
            ->where('payment_status', 'paid')
            ->sum('total_amount_cents') / 100;
    }

    public function getLastBookingDate(): ?string
    {
        $lastBooking = $this->bookings()
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $lastBooking ? $lastBooking->created_at->format('Y-m-d') : null;
    }

    public function addLoyaltyPoints(float $points): void
    {
        $this->increment('loyalty_points', $points);
    }

    public function deductLoyaltyPoints(float $points): void
    {
        $this->decrement('loyalty_points', $points);
    }

    public function blacklist(string $reason): void
    {
        $this->update([
            'is_blacklisted' => true,
            'blacklist_reason' => $reason
        ]);
    }

    public function whitelist(): void
    {
        $this->update([
            'is_blacklisted' => false,
            'blacklist_reason' => null
        ]);
    }
}