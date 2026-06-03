<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'priority' => (int) $this->priority,
            'type_role' => $this->type_role,
            'guard_name' => $this->guard_name,
            'permissions' => $this->relationLoaded('permissions') ? $this->permissions->pluck('name') : [],
        ];
    }
}
