<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'property_id', 'product_id', 'from_location_id', 'to_location_id',
        'quantity', 'unit_cost_cents', 'movement_type', 'reference_type', 'reference_id', 'created_by_user_id'
    ];

    protected $casts = ['quantity' => 'float'];

    public function product() { return $this->belongsTo(Product::class); }
    public function fromLocation() { return $this->belongsTo(StockLocation::class, 'from_location_id'); }
    public function toLocation() { return $this->belongsTo(StockLocation::class, 'to_location_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by_user_id'); }
}
