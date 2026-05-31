<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Permission\Models\Role as SpatieRole;

#[Fillable([
    'name',
    'slug',
    'type_role',
    'color',
    'priority',
    'is_active',
    'description',
    'guard_name',
])]
class Role extends SpatieRole
{
    use HasUlids;
}
