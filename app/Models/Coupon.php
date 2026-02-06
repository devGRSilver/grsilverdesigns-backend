<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'usage_limit',
        'usage_count',
        'user_limit',
        'min_purchase_amount',
        'min_items',
        'starts_at',
        'expires_at',
        'status',
        'first_order_only',
        'free_shipping',
        'included_products',
        'included_categories',
        'included_users',
    ];


    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'status' => 'boolean',
        'first_order_only' => 'boolean',
        'free_shipping' => 'boolean',
        'included_products' => 'array',
        'included_categories' => 'array',
        'included_users' => 'array',
        'value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'user_limit' => 'integer',
        'min_items' => 'integer',
    ];
}
