<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'order_code'             => $this->order_code,
            'customer_id'            => $this->customer_id,
            'customer'               => $this->customer ? $this->customer->name : '-',
            'customer_email'         => $this->customer ? $this->customer->email : '',
            'customer_phone'         => $this->customer ? $this->customer->phone : '',
            'outlet_id'              => $this->outlet_id,
            'outlet'                 => $this->outlet ? $this->outlet->name : '-',
            'service_type'           => $this->service_type,
            'weight'                 => (float) $this->weight,
            'price_per_unit'         => (int) $this->price_per_unit,
            'total_price'            => (int) $this->total_price,
            'order_status'           => $this->order_status,
            'payment_status'         => $this->payment_status,
            'payment_method'         => $this->payment_method ?? 'Tunai',
            'notes'                  => $this->notes ?? '',
            'created_at'             => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'created_at_formatted'   => $this->created_at ? $this->created_at->format('d M Y H:i') : '-',
            'finished_at'            => $this->finished_at ? $this->finished_at->toDateTimeString() : null,
            'finished_at_formatted'  => $this->finished_at ? $this->finished_at->format('d M Y H:i') : '-',
        ];
    }
}
