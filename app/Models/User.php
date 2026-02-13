<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use HasFactory, Notifiable, SoftDeletes, HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'phonecode',
        'phone',
        'email',
        'password',
        'phone_verified_at',
        'email_verified_at',

        'profile_picture',
        'status',
        'profile_complete',

        'last_login_at',
        'device_type',
        'device_token',
        'user_agent',
        'ip_address',

        'country',
        'country_name',
        'city',
        'timezone',
        'currency',
        'latitude',
        'longitude',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'profile_complete' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getProfilePictureAttribute($value)
    {
        return $value
            ? asset($value)
            : asset('default_images/no_user.png');
    }



    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    protected function orderCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->orders_count
                ?? $this->orders()->count()
        );
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
