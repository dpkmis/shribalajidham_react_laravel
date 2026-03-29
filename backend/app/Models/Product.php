<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'property_id', 'sku', 'name', 'description', 'product_type', 'unit', 'cost_cents', 'price_cents',
        'reorder_threshold', 'is_active', 'meta'
    ];

    protected $casts = ['meta' => 'array', 'is_active' => 'boolean'];

    public function property() { return $this->belongsTo(Property::class); }
    public function purchaseOrderLines() { return $this->hasMany(PurchaseOrderLine::class); }
    public function stockMovements() { return $this->hasMany(StockMovement::class); }
    public function stockLevels() { return $this->hasMany(StockLevel::class); }
}
