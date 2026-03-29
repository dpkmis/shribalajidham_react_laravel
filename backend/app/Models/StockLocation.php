<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    protected $fillable = ['property_id', 'name', 'code', 'location_type', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function stockLevels() { return $this->hasMany(StockLevel::class, 'location_id'); }
}
