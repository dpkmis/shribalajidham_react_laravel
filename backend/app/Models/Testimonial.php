<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'guest_name', 'guest_location', 'rating',
        'review_text', 'avatar', 'stay_date', 'source',
        'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'stay_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderByDesc('created_at'); }
}
