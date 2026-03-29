<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'inventory_item_id', 'property_id', 'user_id', 'transaction_type',
        'quantity', 'balance_after', 'unit_price_cents', 'reference_type',
        'reference_id', 'from_location_id', 'to_location_id', 'remarks',
        'transaction_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'unit_price_cents' => 'integer',
        'transaction_date' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($transaction) {
            // Update item stock after transaction
            $item = $transaction->item;
            $item->current_stock = $transaction->balance_after;
            
            // Update last purchase price if it's a stock_in transaction
            if ($transaction->transaction_type === 'stock_in' && $transaction->unit_price_cents) {
                $item->last_purchase_price_cents = $transaction->unit_price_cents;
            }
            
            $item->save();
        });
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUnitPriceAttribute()
    {
        return $this->unit_price_cents ? $this->unit_price_cents / 100 : 0;
    }

    public static function getTransactionTypes()
    {
        return [
            'stock_in' => 'Stock In',
            'stock_out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            'transfer' => 'Transfer',
            'damage' => 'Damage',
            'expired' => 'Expired',
            'return' => 'Return',
        ];
    }
}
