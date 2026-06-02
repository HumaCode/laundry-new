<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Master\Outlet;

#[Fillable([
    'name',
    'code',
    'brand',
    'category',
    'emoji',
    'color',
    'stock',
    'min_stock',
    'max_stock',
    'unit',
    'price',
    'outlet_id',
    'desc',
    'last_restock',
    'last_restock_qty',
    'history',
])]
class Inventory extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'inventories';

    /**
     * Get the modern class-based casts array.
     */
    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'min_stock' => 'integer',
            'max_stock' => 'integer',
            'price' => 'integer',
            'last_restock' => 'date:Y-m-d',
            'last_restock_qty' => 'integer',
            'history' => 'array',
        ];
    }

    /**
     * Get the outlet associated with the inventory item.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}
