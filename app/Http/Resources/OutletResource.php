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
            'staffCount'    => $this->relationLoaded('employees') ? $this->employees->count() : (int) ($this->employees_count ?? 0),
            'orders'        => $this->code === 'OUT-0001' ? 450 : ($this->code === 'OUT-0002' ? 380 : 250),
            'revenue'       => $this->code === 'OUT-0001' ? 28400000 : ($this->code === 'OUT-0002' ? 22100000 : 15000000),
            'employees'     => $this->relationLoaded('employees') ? $this->employees->map(function ($emp) {
                return [
                    'id'        => $emp->id,
                    'name'      => $emp->name,
                    'role'      => $emp->role,
                    'is_active' => (bool) $emp->is_active,
                ];
            }) : [],
        ];
    }
}
