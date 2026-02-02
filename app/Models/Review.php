<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rating',
        'comment',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => 'boolean',
    ];

    /* ================= Relationships ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ================= Scopes ================= */

    // Approved reviews only
    public function scopeApproved($query)
    {
        return $query->where('status', true);
    }

    // Filter by rating
    public function scopeRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }
}
