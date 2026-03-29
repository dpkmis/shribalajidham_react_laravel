<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['property_id', 'slug', 'title', 'content', 'published_at', 'status', 'meta_title', 'meta_description', 'meta'];
    protected $casts = ['published_at' => 'datetime', 'meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function media() { return $this->morphMany(Media::class, 'mediable'); }
}
