<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeFeature extends Model
{
    protected $table = 'room_type_feature';
    protected $fillable = ['room_type_id', 'room_feature_id'];
}
