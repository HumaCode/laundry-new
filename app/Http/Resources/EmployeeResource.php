<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'outlet_id'           => $this->outlet_id,
            'outlet'              => $this->outlet ? [
                'id'   => $this->outlet->id,
                'name' => $this->outlet->name,
            ] : null,
            'name'                => $this->name,
            'code'                => $this->code,
            'phone'               => $this->phone,
            'email'               => $this->email,
            'role'                => $this->role,
            'is_active'           => (bool) $this->is_active,
            'address'             => $this->address,
            'joined_at'           => $this->joined_at ? $this->joined_at->toDateString() : null,
            'joined_at_formatted' => $this->joined_at ? tgl_indo($this->joined_at->toDateString()) : '-',
            'joined'              => $this->created_at ? $this->created_at->toDateString() : null,
        ];
    }
}
