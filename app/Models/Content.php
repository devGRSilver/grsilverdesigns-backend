<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'description',
        'image',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'meta_keywords' => 'array', // JSON ⇄ Array
        'status'        => 'boolean',
    ];
}
