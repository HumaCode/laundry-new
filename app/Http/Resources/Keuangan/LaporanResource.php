<?php

namespace App\Http\Resources\Keuangan;

use Illuminate\Http\Resources\Json\JsonResource;

class LaporanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'totalRevenue' => $this['totalRevenue'],
            'totalOrders' => $this['totalOrders'],
            'totalCustomers' => $this['totalCustomers'],
            'avgRating' => $this['avgRating'],
            'revenueGrowth' => $this['revenueGrowth'],
            'prevMonthStart' => $this['prevMonthStart'],
            'prevMonthEnd' => $this['prevMonthEnd'],
            'prevRevenueSum' => $this['prevRevenueSum'],
            'dateFrom' => $this['dateFrom'],
            'dateTo' => $this['dateTo'],
            'outletId' => $this['outletId'],
            'laporanData' => $this['laporanData'],
        ];
    }
}
