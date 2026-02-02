<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'url',
        'type',
        'read_at',
    ];

    /**
     * The user who owns this notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
