<?php

namespace App\Models\Konfigurasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

#[Fillable([
    'name',
    'url',
    'category',
    'icon',
    'is_active',
    'orders',
])]
class Menu extends Model
{
    use HasUlids;

    /**
     * Scope a query to only include active menus.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', '1');
    }
}
