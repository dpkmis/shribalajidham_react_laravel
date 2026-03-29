<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRatePlan extends Model
{
    protected $fillable = [
        'property_id',
        'room_type_id',
        'name',
        'code',
        'rate_cents',
        'valid_from',
        'valid_to',
        'day_of_week',
        'is_active',
        'min_stay',
        'max_stay',
        'meta'
    ];

    protected $casts = [
        'rate_cents' => 'integer',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'day_of_week' => 'array',
        'is_active' => 'boolean',
        'min_stay' => 'integer',
        'max_stay' => 'integer',
        'meta' => 'array'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function getRateAttribute(): float
    {
        return $this->rate_cents / 100;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValidOn($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->where('valid_from', '<=', $date)
              ->where(function ($q2) use ($date) {
                  $q2->whereNull('valid_to')
                     ->orWhere('valid_to', '>=', $date);
              });
        });
    }
}
