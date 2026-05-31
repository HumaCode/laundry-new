<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'code'          => $this->code,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'city'          => $this->city,
            'manager'       => $this->manager,
            'address'       => $this->address,
            'is_active'     => (bool) $this->is_active,
            'payment_type'  => $this->payment_type,
            'dp_percentage' => (int) $this->dp_percentage,
            'joined'        => $this->created_at ? $this->created_at->toDateString() : null,
            // Optional UI styling and stats simulation
            'staffCount'    => $this->code === 'OUT-0001' ? 15 : ($this->code === 'OUT-0002' ? 12 : 8),
            'orders'        => $this->code === 'OUT-0001' ? 450 : ($this->code === 'OUT-0002' ? 380 : 250),
            'revenue'       => $this->code === 'OUT-0001' ? 28400000 : ($this->code === 'OUT-0002' ? 22100000 : 15000000),
        ];
    }
}
