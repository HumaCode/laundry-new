<?php

namespace App\Repositories\Contracts;

interface LaporanRepositoryInterface
{
    public function getOrdersBaseQuery(?string $dateFrom, ?string $dateTo, ?string $outletId);
    public function getTotalCustomers(?string $outletId): int;
    public function getPrevRevenueSum(string $prevMonthStart, string $prevMonthEnd, ?string $outletId): float;
    public function getDailyRevenueTrend($baseQuery): \Illuminate\Support\Collection;
    public function getPaymentMethodsDist($baseQuery): \Illuminate\Support\Collection;
    public function getOutletRevenueComp(?string $dateFrom, ?string $dateTo, string $prevMonthStart, string $prevMonthEnd): \Illuminate\Support\Collection;
    public function getServiceDistribution($baseQuery): \Illuminate\Support\Collection;
    public function getOrderStatusDist($baseQuery): \Illuminate\Support\Collection;
    public function getCustomerTiersDist(): \Illuminate\Support\Collection;
    public function getTopCustomers(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection;
    public function getKurirTrips(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection;
    public function getPeakHoursDist($baseQuery): \Illuminate\Support\Collection;
    public function getWeekdayDist($baseQuery): \Illuminate\Support\Collection;
    public function getWeeklyOrdersStats(\Carbon\Carbon $start, \Carbon\Carbon $weekEnd, ?string $outletId): array;
}
