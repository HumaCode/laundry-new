<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $outlets = $this->relationLoaded('outlets') ? $this->outlets : collect();

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'code'         => $this->code,
            'owner'        => $this->owner,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'city'         => $this->city,
            'address'      => $this->address,
            'description'  => $this->description,
            'is_active'    => (bool) $this->is_active,
            'outlets_count'=> $this->relationLoaded('outlets')
                               ? $outlets->count()
                               : (int) ($this->outlets_count ?? 0),
            'active_outlets' => $this->relationLoaded('outlets')
                               ? $outlets->where('is_active', true)->count()
                               : 0,
            'total_employees' => $this->relationLoaded('outlets')
                               ? $outlets->sum(fn($o) => $o->relationLoaded('employees') ? $o->employees->count() : 0)
                               : 0,
            'outlets'      => $this->relationLoaded('outlets')
                               ? $outlets->map(fn($o) => [
                                    'id'        => $o->id,
                                    'name'      => $o->name,
                                    'code'      => $o->code,
                                    'city'      => $o->city,
                                    'is_active' => (bool) $o->is_active,
                                    'employees_count' => $o->relationLoaded('employees') ? $o->employees->count() : 0,
                               ])
                               : [],
            'created_at'   => $this->created_at ? $this->created_at->toDateString() : null,
        ];
    }
}
