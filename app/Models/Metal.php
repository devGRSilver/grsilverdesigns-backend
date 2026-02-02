<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price_per_gram',
        'currency'
    ];
}
