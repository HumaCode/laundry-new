<?php

namespace App\Http\Controllers;

use App\Services\Contracts\LaporanServiceInterface;
use App\Models\Master\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected LaporanServiceInterface $laporanService;

    public function __construct(LaporanServiceInterface $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    /**
     * Display the admin dashboard with dynamic statistics.
     */
    public function index(Request $request): View
    {
        $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = Carbon::now()->format('Y-m-d');

        $reportsData = $this->laporanService->getReportsData([
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);

        $recentOrders = Order::with(['customer', 'outlet'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('pages.dashboard', array_merge($reportsData, [
            'recentOrders' => $recentOrders
        ]));
    }
}
