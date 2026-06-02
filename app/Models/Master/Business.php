<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'code',
    'owner',
    'phone',
    'email',
    'city',
    'address',
    'description',
    'is_active',
])]
class Business extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'businesses';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Bisnis memiliki banyak outlet.
     */
    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class, 'business_id');
    }
}
