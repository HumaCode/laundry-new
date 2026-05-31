<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[Fillable([
    'name',
    'guard_name',
    'is_active',
])]
class Permission extends SpatiePermission
{
    use HasUlids;

    public function menus()
    {
        return $this->belongsToMany(\App\Models\Konfigurasi\Menu::class, 'menu_permission');
    }
}
