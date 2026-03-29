<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'title', 'slug', 'subtitle', 'excerpt',
        'content', 'image', 'icon', 'read_time_min', 'author',
        'is_published', 'sort_order', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) $m->slug = Str::slug($m->title);
            if (empty($m->published_at) && $m->is_published) $m->published_at = now();
        });
    }

    public function scopePublished($q) { return $q->where('is_published', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderByDesc('published_at'); }
}
