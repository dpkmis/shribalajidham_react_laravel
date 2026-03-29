<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['property_id', 'file_name', 'file_path', 'disk', 'mime_type', 'size_bytes', 'mediable_type', 'mediable_id', 'alt_text', 'sort_order', 'created_by_user_id'];

    public function mediable() { return $this->morphTo(); }
    public function property() { return $this->belongsTo(Property::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by_user_id'); }
}
