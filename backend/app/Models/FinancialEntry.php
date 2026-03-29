<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialEntry extends Model
{
    protected $fillable = ['financial_transaction_id', 'account_id', 'debit_cents', 'credit_cents', 'narration'];

    public function transaction() { return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id'); }
    public function account() { return $this->belongsTo(Account::class); }
}
