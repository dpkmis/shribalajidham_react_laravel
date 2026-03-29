<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['property_id', 'name', 'email', 'phone', 'reference_code', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
}
