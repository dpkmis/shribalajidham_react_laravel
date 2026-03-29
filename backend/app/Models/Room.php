<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'room_number',
        'room_type_id',
        'floor',
        'block',
        'wing',
        'status',
        'housekeeping_status',
        'price_override_cents',
        'is_smoking',
        'is_accessible',
        'is_connecting',
        'connecting_room_id',
        'is_active',
        'sort_order',
        'last_maintenance_at',
        'notes',
        'meta'
    ];

    protected $casts = [
        'floor' => 'integer',
        'price_override_cents' => 'integer',
        'is_smoking' => 'boolean',
        'is_accessible' => 'boolean',
        'is_connecting' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'last_maintenance_at' => 'datetime',
        'meta' => 'array',
        'deleted_at' => 'datetime'
    ];

    protected $appends = ['current_rate', 'status_label'];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function connectingRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'connecting_room_id');
    }

    public function connectedRooms(): HasMany
    {
        return $this->hasMany(Room::class, 'connecting_room_id');
    }

    // Accessors
    public function getCurrentRateAttribute(): float
    {
        if ($this->price_override_cents) {
            return $this->price_override_cents / 100;
        }
        return $this->roomType->default_rate ?? 0;
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('-', ' ', $this->status));
    }

    public function getPriceOverrideAttribute(): ?float
    {
        return $this->price_override_cents ? $this->price_override_cents / 100 : null;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->where('is_active', true)
                    ->where('housekeeping_status', 'clean');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('room_type_id', $typeId);
    }

    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor', $floor);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('floor')->orderBy('room_number');
    }

    // Helper Methods
    public function isAvailable(): bool
    {
        return $this->status === 'available' 
            && $this->is_active 
            && $this->housekeeping_status === 'clean';
    }

    public function markAsOccupied(): void
    {
        $this->update([
            'status' => 'occupied',
            'housekeeping_status' => 'dirty'
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => 'available',
            'housekeeping_status' => 'clean'
        ]);
    }

    public function needsCleaning(): bool
    {
        return in_array($this->housekeeping_status, ['dirty', 'pickup']);
    }

    // Status Constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_OUT_OF_ORDER = 'out-of-order';
    const STATUS_BLOCKED = 'blocked';

    const HOUSEKEEPING_CLEAN = 'clean';
    const HOUSEKEEPING_DIRTY = 'dirty';
    const HOUSEKEEPING_INSPECTED = 'inspected';
    const HOUSEKEEPING_OUT_OF_SERVICE = 'out-of-service';
    const HOUSEKEEPING_PICKUP = 'pickup';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_OUT_OF_ORDER => 'Out of Order',
            self::STATUS_BLOCKED => 'Blocked',
        ];
    }

    public static function getHousekeepingStatusOptions(): array
    {
        return [
            self::HOUSEKEEPING_CLEAN => 'Clean',
            self::HOUSEKEEPING_DIRTY => 'Dirty',
            self::HOUSEKEEPING_INSPECTED => 'Inspected',
            self::HOUSEKEEPING_OUT_OF_SERVICE => 'Out of Service',
            self::HOUSEKEEPING_PICKUP => 'Pickup',
        ];
    }



    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class);
    }

    public function latestHousekeepingTask()
    {
        return $this->hasOne(HousekeepingTask::class)->latestOfMany();
    }

    // Add this scope:
    public function scopeNeedsCleaning($query)
    {
        return $query->whereIn('housekeeping_status', ['dirty', 'pickup']);
    }

}
