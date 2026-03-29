<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['property_id', 'name', 'location', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function items() { return $this->hasMany(MenuItem::class); }
}
