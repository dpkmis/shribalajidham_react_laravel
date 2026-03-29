<?php
// app/Models/RoomType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'name',
        'code',
        'description',
        'default_rate_cents',
        'max_occupancy',
        'max_adults',
        'max_children',
        'beds',
        'room_size_sqm',
        'bed_type',
        'is_active',
        'sort_order',
        'amenities',
        'images',
        'meta'
    ];

    protected $casts = [
        'default_rate_cents' => 'integer',
        'max_occupancy' => 'integer',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'beds' => 'integer',
        'room_size_sqm' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'amenities' => 'array',
        'images' => 'array',
        'meta' => 'array',
        'deleted_at' => 'datetime'
    ];

    protected $appends = ['default_rate'];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(RoomFeature::class, 'room_type_feature');
    }

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RoomRatePlan::class);
    }

    // Accessors
    public function getDefaultRateAttribute(): float
    {
        return $this->default_rate_cents / 100;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper Methods
    public function getAvailableRoomsCount(): int
    {
        return $this->rooms()->where('status', 'available')->count();
    }

    public function getTotalRoomsCount(): int
    {
        return $this->rooms()->count();
    }
}
