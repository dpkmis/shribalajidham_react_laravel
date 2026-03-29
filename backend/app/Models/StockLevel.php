<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    protected $fillable = ['property_id', 'product_id', 'location_id', 'quantity', 'last_cost_cents'];
    protected $casts = ['quantity' => 'float'];

    public function product() { return $this->belongsTo(Product::class); }
    public function location() { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function property() { return $this->belongsTo(Property::class); }
}
