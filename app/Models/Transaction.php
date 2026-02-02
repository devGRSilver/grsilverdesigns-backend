<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'transaction_id',
        'amount',
        'currency_code',
        'status',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'gateway_payment_id',
        'gateway_order_id',
        'gateway_signature',
        'customer_email',
        'customer_phone',
        'customer_ip',
        'user_agent',
        'gateway_fee',
        'tax_on_fee',
        'net_amount',
        'settled_at',
        'refunded_at',
        'failed_at',
        'metadata',
        'notes',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'metadata'       => 'array',
        'notes'          => 'array',
        'amount'         => 'decimal:4',
        'net_amount'     => 'decimal:4',
        'gateway_fee'    => 'decimal:4',
        'tax_on_fee'     => 'decimal:4',
        'settled_at'     => 'datetime',
        'refunded_at'    => 'datetime',
        'failed_at'      => 'datetime',
        'status' => TransactionStatus::class,
    ];

    /**
     * Relationships
     */

    // Transaction belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Transaction belongs to an Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scopes
     */

    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', TransactionStatus::FAILED);
    }
}
