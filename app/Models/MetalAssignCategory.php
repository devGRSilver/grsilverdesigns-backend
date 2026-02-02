<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetalAssignCategory extends Model
{


    protected $fillable = [
        'metal_id',
        'category_id',
        'sub_category_id',
    ];
}
