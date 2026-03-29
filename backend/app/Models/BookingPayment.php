<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPayment extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_reference',
        'amount_cents',
        'method',
        'type',
        'card_last4',
        'transaction_id',
        'cheque_number',
        'cheque_date',
        'bank_name',
        'gateway',
        'gateway_response',
        'status',
        'paid_at',
        'remarks',
        'received_by_user_id'
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'cheque_date' => 'date',
        'gateway_response' => 'array',
        'paid_at' => 'datetime'
    ];

    protected $appends = ['amount'];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    // Accessors
    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('paid_at', today());
    }

    // Payment Method Constants
    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_UPI = 'upi';
    const METHOD_NET_BANKING = 'net-banking';
    const METHOD_CHEQUE = 'cheque';
    const METHOD_WALLET = 'wallet';
    const METHOD_BANK_TRANSFER = 'bank-transfer';
    const METHOD_OTHER = 'other';

    // Payment Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    // Payment Type Constants
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';

    public static function getMethodOptions(): array
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_CARD => 'Card',
            self::METHOD_UPI => 'UPI',
            self::METHOD_NET_BANKING => 'Net Banking',
            self::METHOD_CHEQUE => 'Cheque',
            self::METHOD_WALLET => 'Wallet',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_OTHER => 'Other',
        ];
    }
}

