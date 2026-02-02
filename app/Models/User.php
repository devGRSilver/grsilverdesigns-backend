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
        'phone',
        'phone_code',
        'phone_verified_at',
        'email',
        'email_verified_at',
        'password',
        'profile_picture',
        'timezone',
        'currency',
        'device_type',
        'device_token',
        'ip_address',
        'user_agent',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
