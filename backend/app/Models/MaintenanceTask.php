<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceTask extends Model
{
    protected $fillable = ['property_id', 'room_id', 'severity', 'description', 'reported_by_user_id', 'status', 'reported_at', 'resolved_at', 'meta'];
    protected $casts = ['reported_at' => 'datetime', 'resolved_at' => 'datetime', 'meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function room() { return $this->belongsTo(Room::class); }
    public function reportedBy() { return $this->belongsTo(User::class, 'reported_by_user_id'); }
}
