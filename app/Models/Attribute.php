<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope for active attributes
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Attribute has many values
     */
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
