<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;


    protected $fillable = [
        'category_id',
        'product_type',
        'name',
        'slug',
        'sku',
        'main_image',
        'secondary_image',
        'short_description',
        'description',
        'marketing_label',
        'cost_price',
        'mrp_price',
        'selling_price',
        'tax_percentage',
        'stock_status',
        'is_featured',
        'status',
        'weight',
        'seo_title',
        'seo_image',
        'seo_description',
        'seo_keywords',
        'personalize',
        'variant_attributes',
        'min_price',
        'max_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_type' => 'string',
        'cost_price' => 'decimal:2',
        'mrp_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'weight' => 'decimal:3',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'status' => 'boolean',
        'personalize' => 'boolean',
        'variant_attributes' => 'array',
        'total_stock' => 'integer',
        'deleted_at' => 'datetime',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }



    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id', 'id');
    }



    public function getMainImageAttribute($value)
    {

        if (isset($value)) {
            return $value;
        }
        return asset('assets/no_image.png');
    }


    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }


    public function getTotalVariantQtyAttribute(): int
    {
        return (int) $this->variants()->sum('stock_quantity');
    }
}
