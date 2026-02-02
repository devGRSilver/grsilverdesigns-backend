<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantAttributeCombination extends Model
{
    protected $fillable = [
        'variant_id',
        'attribute_id',
        'attribute_value_id',
    ];

    protected $casts = [
        'variant_id'         => 'integer',
        'attribute_id'       => 'integer',
        'attribute_value_id' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
