<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{

    protected $fillable = [
        'title',
        'type',
        'group_key',
        'image_url',
        'link_url',
        'description',
        'button_text',
        'status',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'status'     => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
