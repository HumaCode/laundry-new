<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'services';

    protected $fillable = [
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
    ];

    protected $casts = [
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
