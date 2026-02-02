<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'sub_total',
        'discount_amount',
        'coupon_code',
        'tax_amount',
        'shipping_amount',
        'grand_total',
        'profit',

        'payment_method',
        'payment_status',
        'transaction_id',

        'shipping_address_id',
        'billing_address_id',

        'shipping_method',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'expected_delivery_date',

        'customer_name',
        'customer_email',
        'customer_phone',

        'notes',
        'metadata',


        // Timestamps
        'created_at',
        'updated_at',
        'cancelled_at',
        'returned_at',
        'refunded_at',
        'failed_at',


    ];

    // Casts
    protected $casts = [
        'status' => OrderStatus::class,
        'notes' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'returned_at' => 'datetime',
        'refunded_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(OrderAddress::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(OrderAddress::class, 'billing_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }






    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id');
    }
}
