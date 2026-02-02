<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'attribute_id' => 'integer',
        'sort_order'   => 'integer',
    ];

    /**
     * Attribute this value belongs to
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
