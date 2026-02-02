<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image_url',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'product_id'         => 'integer',
        'product_variant_id' => 'integer',
        'is_default'         => 'boolean',
        'sort_order'         => 'integer',
    ];


    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
