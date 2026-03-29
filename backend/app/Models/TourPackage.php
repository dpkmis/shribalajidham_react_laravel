<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TourPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'name', 'slug', 'description', 'duration',
        'price_cents', 'price_label', 'group_size', 'places_covered',
        'includes', 'image', 'is_popular', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'includes' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['price'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) $m->slug = Str::slug($m->name);
        });
    }

    public function getPriceAttribute(): float { return $this->price_cents / 100; }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('name'); }
    public function scopeForProperty($q, $id) { return $q->where('property_id', $id); }
}
