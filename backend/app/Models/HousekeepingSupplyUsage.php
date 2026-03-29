<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousekeepingSupplyUsage extends Model
{
    protected $table = 'housekeeping_supplies_usage';

    protected $fillable = [
        'task_id', 'inventory_item_id', 'staff_id', 'quantity_used', 'notes'
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(HousekeepingTask::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function staff()
    {
        return $this->belongsTo(HousekeepingStaff::class);
    }
}

?>