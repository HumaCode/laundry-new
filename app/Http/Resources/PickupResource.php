<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'trip_code'      => $this->trip_code,
            'customer_name'  => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_id'    => $this->customer_id,
            'outlet_id'      => $this->outlet_id,
            'outlet_name'    => $this->outlet ? $this->outlet->name : null,
            'order_code'     => $this->order_code,
            'address_from'   => $this->address_from,
            'address_to'     => $this->address_to,
            'service_type'   => $this->service_type,
            'employee_id'    => $this->employee_id,
            'driver_name'    => $this->driver ? $this->driver->name : null,
            'driver_vehicle' => $this->driver ? ($this->driver->code ? "Motor Kurir - " . $this->driver->code : 'Motor Kurir') : null,
            'distance'       => $this->distance,
            'eta'            => $this->eta,
            'fee'            => (int) $this->fee,
            'scheduled_at'   => $this->scheduled_at ? $this->scheduled_at->format('Y-m-d H:i') : null,
            'scheduled_time' => $this->scheduled_at ? $this->scheduled_at->format('H:i') : null,
            'scheduled_date' => $this->scheduled_at ? $this->scheduled_at->format('Y-m-d') : null,
            'weight'         => $this->weight,
            'notes'          => $this->notes,
            'status'         => $this->status,
            'avatar_color'   => $this->avatar_color,
            'created_at'     => $this->created_at ? $this->created_at->toDateString() : null,
        ];
    }
}
