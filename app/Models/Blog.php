<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'content',
        'featured_image',
        'status',
        'published_at',
        'created_by',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];





    protected $casts = [
        'status' => 'boolean',
        'published_at' => 'datetime',
        'meta_keywords' => 'array',
    ];
}
