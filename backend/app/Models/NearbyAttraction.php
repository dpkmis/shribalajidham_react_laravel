<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NearbyAttraction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'name', 'description', 'distance',
        'travel_time', 'image', 'category', 'highlights',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'highlights' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('name'); }
    public function scopeByCategory($q, $cat) { return $q->where('category', $cat); }
}
