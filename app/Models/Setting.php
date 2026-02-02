<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'site_name',
        'site_tagline',
        'site_logo',
        'site_favicon',
        'email',
        'phone',
        'address',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'youtube',
        'maintenance_mode',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
    ];
}
