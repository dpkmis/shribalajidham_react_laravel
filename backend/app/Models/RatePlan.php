<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatePlan extends Model
{
    protected $fillable = ['property_id', 'code', 'name', 'description', 'is_inventory_blocking'];
    protected $casts = ['is_inventory_blocking' => 'boolean'];

    public function property() { return $this->belongsTo(Property::class); }
    public function roomRates() { return $this->hasMany(RoomRate::class); }
}
