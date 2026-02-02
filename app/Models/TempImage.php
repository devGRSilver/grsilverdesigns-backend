<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempImage extends Model
{
    protected $fillable = [
        'uniq_id',
        'user_id',
        'image_url',
        'model_type',
    ];
}
