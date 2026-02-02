<?php

namespace App\Models;

use App\Constants\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'is_primary',
        'name',
        'sort_order',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'image',
        'banner_image',
        'status',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'is_primary' => 'boolean',
        'parent_id'  => 'integer',
    ];


    /**
     * Parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }



    /**
     * Child categories
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /* -------------------- Scopes -------------------- */

    public function scopeParentCategory($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildCategory($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Constant::ACTIVE);
    }

    public function scopeInActive($query)
    {
        return $query->where('status', Constant::IN_ACTIVE);
    }

    /* -------------------- Accessors (Optional) -------------------- */

    public function getMetaKeywordsArrayAttribute(): array
    {
        return $this->meta_keywords
            ? json_decode($this->meta_keywords, true)
            : [];
    }
}
