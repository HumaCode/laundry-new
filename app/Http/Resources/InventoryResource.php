<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $stock = $this->stock;
        $minS = $this->min_stock;
        $maxS = $this->max_stock;
        $pct = $maxS > 0 ? ($stock / $maxS) : 0;

        if ($stock === 0) {
            $status = 'habis';
        } elseif ($stock < $minS * 0.5) {
            $status = 'kritis';
        } elseif ($stock < $minS) {
            $status = 'rendah';
        } elseif ($pct > 0.8) {
            $status = 'lebih';
        } else {
            $status = 'cukup';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'brand' => $this->brand,
            'category' => $this->category,
            'emoji' => $this->emoji ?? '📦',
            'color' => $this->color ?? '#6366F1',
            'stock' => $this->stock,
            'minStock' => $this->min_stock,
            'maxStock' => $this->max_stock,
            'unit' => $this->unit,
            'price' => (int) $this->price,
            'value' => $this->stock * $this->price,
            'status' => $status,
            'outlet' => $this->outlet ? $this->outlet->name : '—',
            'outlet_id' => $this->outlet_id,
            'desc' => $this->desc,
            'lastRestock' => $this->last_restock ? $this->last_restock->format('Y-m-d') : null,
            'lastRestockQty' => $this->last_restock_qty,
            'history' => $this->history ?? [],
        ];
    }
}
