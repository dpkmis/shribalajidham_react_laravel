<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    protected $fillable = ['property_id', 'reference_type', 'reference_id', 'description', 'transaction_date', 'total_cents', 'meta'];
    protected $casts = ['transaction_date' => 'date', 'meta' => 'array'];

    public function property() { return $this->belongsTo(Property::class); }
    public function entries() { return $this->hasMany(FinancialEntry::class); }
}
