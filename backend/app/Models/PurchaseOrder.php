<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = ['property_id', 'po_number', 'supplier_id', 'status', 'expected_date', 'total_cents', 'meta'];
    protected $casts = ['expected_date' => 'date', 'meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function lines() { return $this->hasMany(PurchaseOrderLine::class); }
}
