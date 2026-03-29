<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['property_id', 'code', 'name', 'type', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function financialEntries() { return $this->hasMany(FinancialEntry::class); }
}
