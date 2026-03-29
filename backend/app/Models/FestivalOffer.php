<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FestivalOffer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'name', 'slug', 'hindi_name', 'description',
        'festival_month', 'price_cents', 'per_night_cents', 'nights',
        'highlight_badge', 'includes', 'image', 'gradient_from',
        'gradient_to', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'per_night_cents' => 'integer',
        'includes' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['price', 'per_night'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) $m->slug = Str::slug($m->name);
        });
    }

    public function getPriceAttribute(): float { return $this->price_cents / 100; }
    public function getPerNightAttribute(): ?float { return $this->per_night_cents ? $this->per_night_cents / 100 : null; }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('name'); }
}
