<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'sku',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'quantity',
        'total',
        'variant_name',
        'variant_options',
        'quantity_returned',
        'amount_refunded',
        'returned_at',
        'product_metadata',
        'metadata',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'unit_price'        => 'decimal:4',
        'discount_amount'   => 'decimal:4',
        'tax_amount'        => 'decimal:4',
        'total'             => 'decimal:4',
        'quantity'          => 'integer',
        'quantity_returned' => 'integer',
        'amount_refunded'   => 'decimal:4',
        'variant_options'   => 'array',
        'product_metadata'  => 'array',
        'metadata'          => 'array',
        'returned_at'       => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /**
     * Relationships
     */

    // OrderItem belongs to an Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // OrderItem may belong to a Product (optional)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helper Methods
     */

    // Check if the item is fully returned
    // public function isFullyReturned(): bool
    // {
    //     return $this->quantity_returned >= $this->quantity;
    // }

    // // Calculate remaining refundable amount
    // public function refundableAmount(): float
    // {
    //     return max($this->total - $this->amount_refunded, 0);
    // }
}
