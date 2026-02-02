<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'mrp_price',
        'selling_price',
        'cost_price',
        'weight',
        'tax_percentage',
        'stock_quantity',
        'is_default',
        'stock_status',
        'status',
    ];


    protected $casts = [
        'mrp_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'tax_percentage' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_default' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_combinations',
            'variant_id',
            'attribute_value_id'
        );
    }
}
