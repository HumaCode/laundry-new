<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'service_code' => $this->service_code,
            'name'         => $this->name,
            'emoji'        => $this->emoji,
            'category'     => $this->category,
            'description'  => $this->description,
            'price'        => (int) $this->price,
            'unit'         => $this->unit,
            'eta'          => $this->eta,
            'color'        => $this->color,
            'status'       => (bool) $this->status,
            'express'      => (bool) $this->express,
            'pickup'       => (bool) $this->pickup,
            'target'       => (int) $this->target,
            'min_qty'      => $this->min_qty,
            'features'     => $this->features ?? [],
            'tiers'        => $this->tiers ?? [],
            'orders'       => (int) $this->orders,
            'revenue'      => (int) $this->revenue,
            'created_at'   => $this->created_at ? $this->created_at->toDateString() : null,
        ];
    }
}
