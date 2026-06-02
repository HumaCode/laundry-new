<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'service_code',
    'name',
    'emoji',
    'category',
    'description',
    'price',
    'unit',
    'eta',
    'color',
    'status',
    'express',
    'pickup',
    'target',
    'min_qty',
    'features',
    'tiers',
    'orders',
    'revenue',
])]
class Service extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'services';

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'status' => 'boolean',
            'express' => 'boolean',
            'pickup' => 'boolean',
            'target' => 'integer',
            'orders' => 'integer',
            'revenue' => 'integer',
            'features' => 'array',
            'tiers' => 'array',
        ];
    }
}
