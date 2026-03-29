<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'category_id', 'item_code', 'name', 'description',
        'unit', 'current_stock', 'min_stock', 'max_stock', 'reorder_point',
        'unit_price_cents', 'last_purchase_price_cents',
        'storage_location', 'bin_location', 'supplier_name', 'supplier_code',
        'expiry_date', 'batch_number', 'is_perishable', 'requires_approval',
        'status', 'is_active', 'notes', 'sort_order'
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'unit_price_cents' => 'integer',
        'last_purchase_price_cents' => 'integer',
        'is_perishable' => 'boolean',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'expiry_date' => 'date',
    ];

    protected $appends = ['unit_price', 'last_purchase_price', 'stock_value', 'stock_status'];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Accessors
    public function getUnitPriceAttribute()
    {
        return $this->unit_price_cents ? $this->unit_price_cents / 100 : 0;
    }

    public function getLastPurchasePriceAttribute()
    {
        return $this->last_purchase_price_cents ? $this->last_purchase_price_cents / 100 : 0;
    }

    public function getStockValueAttribute()
    {
        return $this->current_stock * $this->unit_price;
    }

    public function getStockStatusAttribute()
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->current_stock <= $this->min_stock) {
            return 'low_stock';
        } elseif ($this->current_stock <= $this->reorder_point) {
            return 'reorder';
        } elseif ($this->current_stock >= $this->max_stock) {
            return 'overstock';
        }
        return 'normal';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= min_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('is_perishable', true)
                     ->whereNotNull('expiry_date')
                     ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Methods
    public function addStock($quantity, $userId, $remarks = null, $unitPrice = null)
    {
        return InventoryTransaction::create([
            'inventory_item_id' => $this->id,
            'property_id' => $this->property_id,
            'user_id' => $userId,
            'transaction_type' => 'stock_in',
            'quantity' => abs($quantity),
            'balance_after' => $this->current_stock + abs($quantity),
            'unit_price_cents' => $unitPrice ? $unitPrice * 100 : $this->unit_price_cents,
            'remarks' => $remarks,
        ]);
    }

    public function removeStock($quantity, $userId, $remarks = null, $referenceType = null, $referenceId = null)
    {
        if ($this->current_stock < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$this->current_stock}, Requested: {$quantity}");
        }

        return InventoryTransaction::create([
            'inventory_item_id' => $this->id,
            'property_id' => $this->property_id,
            'user_id' => $userId,
            'transaction_type' => 'stock_out',
            'quantity' => -abs($quantity),
            'balance_after' => $this->current_stock - abs($quantity),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'remarks' => $remarks,
        ]);
    }

    public function adjustStock($newQuantity, $userId, $remarks)
    {
        $difference = $newQuantity - $this->current_stock;
        
        return InventoryTransaction::create([
            'inventory_item_id' => $this->id,
            'property_id' => $this->property_id,
            'user_id' => $userId,
            'transaction_type' => 'adjustment',
            'quantity' => $difference,
            'balance_after' => $newQuantity,
            'remarks' => $remarks,
        ]);
    }

    public static function getStatusOptions()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'discontinued' => 'Discontinued',
        ];
    }
}
