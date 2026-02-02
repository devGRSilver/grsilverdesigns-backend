<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'status',
    ];





    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'permission_group_id');
    }
}
