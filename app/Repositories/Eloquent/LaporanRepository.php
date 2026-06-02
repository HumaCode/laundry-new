<?php

namespace App\Repositories\Eloquent;

use App\Models\Master\Order;
use App\Models\Master\Outlet;
use App\Models\Master\Employee;
use App\Models\User;
use App\Models\Operasional\Pickup;
use App\Repositories\Contracts\LaporanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanRepository implements LaporanRepositoryInterface
{
    /**
     * Get base query for orders filtered by date range and outlet.
     */
    public function getOrdersBaseQuery(?string $dateFrom, ?string $dateTo, ?string $outletId)
    {
        $query = Order::query();
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
        }
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        return $query;
    }

    /**
     * Get total customer count.
     */
    public function getTotalCustomers(?string $outletId): int
    {
        $query = User::role('customer');
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        return $query->count();
    }

    /**
     * Get revenue sum for the previous month.
     */
    public function getPrevRevenueSum(string $prevMonthStart, string $prevMonthEnd, ?string $outletId): float
    {
        $query = Order::where('payment_status', 'Lunas')
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd]);
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        return (float) $query->sum('total_price');
    }

    /**
     * Get daily revenue trend.
     */
    public function getDailyRevenueTrend($baseQuery): \Illuminate\Support\Collection
    {
        $isSqlite = DB::getDriverName() === 'sqlite';
        $dateRaw = $isSqlite ? "strftime('%Y-%m-%d', created_at) as date" : 'DATE(created_at) as date';

        return (clone $baseQuery)
            ->select(DB::raw($dateRaw), DB::raw('SUM(total_price) as total'), DB::raw('COUNT(*) as count'))
            ->where('payment_status', 'Lunas')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get payment methods distribution.
     */
    public function getPaymentMethodsDist($baseQuery): \Illuminate\Support\Collection
    {
        return (clone $baseQuery)
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('SUM(total_price) as total'))
            ->where('payment_status', 'Lunas')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();
    }

    /**
     * Get outlet revenue comparison.
     */
    public function getOutletRevenueComp(?string $dateFrom, ?string $dateTo, string $prevMonthStart, string $prevMonthEnd): \Illuminate\Support\Collection
    {
        return Outlet::all()->map(function ($outlet) use ($dateFrom, $dateTo, $prevMonthStart, $prevMonthEnd) {
            $currentVal = Order::where('outlet_id', $outlet->id)
                ->where('payment_status', 'Lunas')
                ->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()])
                ->sum('total_price');

            $prevVal = Order::where('outlet_id', $outlet->id)
                ->where('payment_status', 'Lunas')
                ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
                ->sum('total_price');

            return collect([
                'name' => $outlet->name,
                'current' => intval($currentVal),
                'previous' => intval($prevVal)
            ]);
        });
    }

    /**
     * Get service distribution.
     */
    public function getServiceDistribution($baseQuery): \Illuminate\Support\Collection
    {
        return (clone $baseQuery)
            ->select('service_type', DB::raw('count(*) as count'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('service_type')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Get order status distribution.
     */
    public function getOrderStatusDist($baseQuery): \Illuminate\Support\Collection
    {
        return (clone $baseQuery)
            ->select('order_status', DB::raw('count(*) as count'))
            ->groupBy('order_status')
            ->get();
    }

    /**
     * Get customer tiers distribution.
     */
    public function getCustomerTiersDist(): \Illuminate\Support\Collection
    {
        return User::role('customer')
            ->select('tier', DB::raw('count(*) as count'))
            ->groupBy('tier')
            ->get();
    }

    /**
     * Get top customers.
     */
    public function getTopCustomers(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection
    {
        return User::role('customer')
            ->withCount(['orders' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
                }
            }])
            ->withSum(['orders' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
                }
            }], 'total_price')
            ->orderByDesc('orders_sum_total_price')
            ->limit(10)
            ->get();
    }

    /**
     * Get kurir trips.
     */
    public function getKurirTrips(?string $dateFrom, ?string $dateTo): \Illuminate\Support\Collection
    {
        return Employee::where('role', 'Kurir')->get()->map(function ($emp) use ($dateFrom, $dateTo) {
            $tripsCount = Pickup::where('employee_id', $emp->id)
                ->whereBetween('scheduled_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()])
                ->count();
            return collect([
                'name' => $emp->name,
                'trips' => $tripsCount
            ]);
        });
    }

    /**
     * Get peak hours distribution.
     */
    public function getPeakHoursDist($baseQuery): \Illuminate\Support\Collection
    {
        $isSqlite = DB::getDriverName() === 'sqlite';
        $hourRaw = $isSqlite ? "cast(strftime('%H', created_at) as integer) as hour" : 'HOUR(created_at) as hour';

        return (clone $baseQuery)
            ->select(DB::raw($hourRaw), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Get weekday distribution.
     */
    public function getWeekdayDist($baseQuery): \Illuminate\Support\Collection
    {
        $isSqlite = DB::getDriverName() === 'sqlite';
        $weekdayRaw = $isSqlite ? "(cast(strftime('%w', created_at) as integer) + 1) as dayofweek" : 'DAYOFWEEK(created_at) as dayofweek';

        return (clone $baseQuery)
            ->select(DB::raw($weekdayRaw), DB::raw('count(*) as count'))
            ->groupBy('dayofweek')
            ->orderBy('dayofweek')
            ->get();
    }

    /**
     * Get weekly order stats for summary table.
     */
    public function getWeeklyOrdersStats(\Carbon\Carbon $start, \Carbon\Carbon $weekEnd, ?string $outletId): array
    {
        $wOrders = Order::whereBetween('created_at', [$start, $weekEnd]);
        if ($outletId) {
            $wOrders->where('outlet_id', $outletId);
        }
        $count = $wOrders->count();
        $revenue = $wOrders->where('payment_status', 'Lunas')->sum('total_price');

        return [
            'count' => $count,
            'revenue' => (float) $revenue
        ];
    }
}
