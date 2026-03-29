<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'time_zone', 'currency', 'address', 'city', 'country', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function users() { return $this->hasMany(User::class); }
    public function rooms() { return $this->hasMany(Room::class); }
    public function roomTypes() { return $this->hasMany(RoomType::class); }
    public function ratePlans() { return $this->hasMany(RatePlan::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function products() { return $this->hasMany(Product::class); }
    public function suppliers() { return $this->hasMany(Supplier::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function settings() { return $this->hasMany(Setting::class); }
}
