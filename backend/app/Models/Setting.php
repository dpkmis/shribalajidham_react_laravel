<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['property_id', 'key', 'value', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
}
