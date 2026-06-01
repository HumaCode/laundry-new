<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $avColors = ['#6366F1','#10B981','#F59E0B','#EC4899','#3B82F6','#8B5CF6','#F97316','#06B6D4','#14B8A6','#EF4444'];
        $grads = ['grad-purple','grad-green','grad-orange','grad-pink','grad-blue','grad-teal'];
        $services = ['Cuci Setrika','Cuci Kering','Express','Bed Cover','Jas & Blazer','Setrika Saja'];

        $hash = abs(crc32($this->id));
        $color = $avColors[$hash % count($avColors)];
        $grad = $grads[$hash % count($grads)];
        $favService = $services[$hash % count($services)];

        $orders = ($hash % 45) + 3; // 3 to 47 orders
        $avgOrder = 25000 + (($hash % 15) * 10000); // 25k to 165k average order
        $total = $orders * $avgOrder;
        $rating = number_format(4.0 + (($hash % 11) * 0.1), 1); // 4.0 to 5.0 rating

        $joinedDate = $this->created_at ? $this->created_at->toDateString() : date('Y-m-d');
        $lastOrderDate = $this->created_at ? $this->created_at->addDays(min($orders, 28))->toDateString() : date('Y-m-d');
        if (strtotime($lastOrderDate) > time()) {
            $lastOrderDate = date('Y-m-d');
        }

        // Mock recent orders for detail view
        $recentOrders = [
            [
                'id' => 'ORD-2026-' . str_pad($hash % 1000, 4, '0', STR_PAD_LEFT),
                'service' => $favService,
                'amount' => $avgOrder,
                'date' => $lastOrderDate
            ],
            [
                'id' => 'ORD-2026-' . str_pad(($hash + 1) % 1000, 4, '0', STR_PAD_LEFT),
                'service' => $services[($hash + 1) % count($services)],
                'amount' => $avgOrder - 5000 > 10000 ? $avgOrder - 5000 : $avgOrder,
                'date' => date('Y-m-d', strtotime($lastOrderDate . ' - 3 days'))
            ]
        ];

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'username'     => $this->username,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'gender'       => $this->gender,
            'dob'          => $this->dob,
            'address'      => $this->address ?? '-',
            'tier'         => $this->tier ?? 'Baru',
            'notes'        => $this->notes ?? '',
            'outlet_id'    => $this->outlet_id,
            'outlet'       => $this->outlet ? $this->outlet->name : '-',
            'is_active'    => $this->is_active,
            'orders'       => $orders,
            'total'        => $total,
            'avgOrder'     => $avgOrder,
            'rating'       => (float) $rating,
            'joined'       => $joinedDate,
            'lastOrder'    => $lastOrderDate,
            'favService'   => $favService,
            'color'        => $color,
            'grad'         => $grad,
            'recentOrders' => $recentOrders,
        ];
    }
}
