<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousekeepingChecklist extends Model
{
    protected $fillable = [
        'property_id', 'name', 'code', 'checklist_type', 'description',
        'items', 'estimated_duration_minutes', 'is_default', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'items' => 'array',
        'estimated_duration_minutes' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('checklist_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

?>