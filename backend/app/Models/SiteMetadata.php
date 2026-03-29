<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteMetadata extends Model
{
    protected $table = 'site_metadata';

    protected $fillable = [
        'property_id', 'group', 'key', 'label', 'value', 'type', 'sort_order',
    ];

    public function scopeForProperty($q, $id) { return $q->where('property_id', $id); }
    public function scopeByGroup($q, $group) { return $q->where('group', $group); }

    /**
     * Get all metadata as grouped key-value pairs
     */
    public static function getAllGrouped(?int $propertyId = null): array
    {
        $query = static::query()->orderBy('group')->orderBy('sort_order');
        if ($propertyId) $query->forProperty($propertyId);

        $result = [];
        foreach ($query->get() as $row) {
            $result[$row->group][$row->key] = $row->value;
        }
        return $result;
    }

    /**
     * Get flat key-value map
     */
    public static function getAllFlat(?int $propertyId = null): array
    {
        $query = static::query();
        if ($propertyId) $query->forProperty($propertyId);
        return $query->pluck('value', 'key')->toArray();
    }

    /**
     * Get a single value by key
     */
    public static function getValue(string $key, ?int $propertyId = null): ?string
    {
        $query = static::where('key', $key);
        if ($propertyId) $query->forProperty($propertyId);
        return $query->value('value');
    }

    /**
     * Set a value by key
     */
    public static function setValue(string $key, ?string $value, ?int $propertyId = null): void
    {
        static::where('key', $key)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->update(['value' => $value]);
    }
}
