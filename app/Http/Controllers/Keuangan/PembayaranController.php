<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Master\Order;
use App\Models\Master\Outlet;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaginateResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    /**
     * Display payments listing.
     */
    public function index(Request $request)
    {
        $stats = $this->getPaymentStats($request->input('outlet_id'));

        if ($request->wantsJson() || $request->ajax()) {
            $query = Order::with(['customer', 'outlet']);

            // Filters
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('order_code', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->input('payment_status'));
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->input('payment_method'));
            }

            if ($request->filled('outlet_id')) {
                $query->where('outlet_id', $request->input('outlet_id'));
            }

            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->input('date_range'));
                if (count($dates) === 2) {
                    $startDate = Carbon::parse($dates[0])->startOfDay();
                    $endDate = Carbon::parse($dates[1])->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }

            // Sort
            $sort = $request->input('sort', 'recent');
            if ($sort === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } elseif ($sort === 'amount-high') {
                $query->orderBy('total_price', 'desc');
            } elseif ($sort === 'amount-low') {
                $query->orderBy('total_price', 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->input('per_page', 10);
            $payments = $query->paginate($perPage);

            $paginated = new PaginateResource($payments, OrderResource::class);
            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats
            ]);

            return ResponseHelper::jsonResponse(true, 'Data pembayaran berhasil diambil', $responseData, 200);
        }

        $outlets = Outlet::orderBy('name')->get();

        return view('pages.keuangan.pembayaran.index', [
            'topbarTitle' => 'Pembayaran',
            'topbarIcon' => 'fa-credit-card',
            'outlets' => $outlets,
            'stats' => $stats
        ]);
    }

    /**
     * Display specific payment detail.
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::with(['customer', 'outlet'])->findOrFail($id);
        return ResponseHelper::jsonResponse(true, 'Detail pembayaran berhasil diambil', new OrderResource($order), 200);
    }

    /**
     * Update payment details.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:Belum,Lunas',
            'payment_method' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        
        $updateData = [
            'payment_status' => $validated['payment_status'],
            'payment_method' => $validated['payment_status'] === 'Lunas' ? ($validated['payment_method'] ?? 'Tunai') : null,
        ];

        $order->update($updateData);

        return ResponseHelper::jsonResponse(true, 'Status pembayaran berhasil diperbarui', new OrderResource($order), 200);
    }

    /**
     * Helper to compute payment summary statistics.
     */
    private function getPaymentStats(?string $outletId): array
    {
        $revenueQuery = Order::where('payment_status', 'Lunas');
        $receivableQuery = Order::where('payment_status', 'Belum');
        $lunasCountQuery = Order::where('payment_status', 'Lunas');
        $belumCountQuery = Order::where('payment_status', 'Belum');

        if ($outletId) {
            $revenueQuery->where('outlet_id', $outletId);
            $receivableQuery->where('outlet_id', $outletId);
            $lunasCountQuery->where('outlet_id', $outletId);
            $belumCountQuery->where('outlet_id', $outletId);
        }

        return [
            'total_pendapatan' => (int) $revenueQuery->sum('total_price'),
            'total_piutang' => (int) $receivableQuery->sum('total_price'),
            'count_lunas' => $lunasCountQuery->count(),
            'count_belum' => $belumCountQuery->count(),
        ];
    }
}
