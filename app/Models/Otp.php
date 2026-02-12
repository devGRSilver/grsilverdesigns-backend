<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'phonecode',
        'full_phone',
        'phone',
        'otp',
        'token',
        'type',
        'name',
        'timezone',
        'attempts',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'attempts' => 'integer',
    ];

    protected $hidden = [
        'otp',
    ];


    /**
     * Get full phone number
     */
    public function getFullPhoneAttribute(): string
    {
        return $this->phonecode . $this->phone;
    }
}
