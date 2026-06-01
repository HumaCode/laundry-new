<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaginateResource;
use App\Helpers\ResponseHelper;
use App\Services\OrderService;
use App\Services\OutletService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $outletService;

    public function __construct(OrderService $orderService, OutletService $outletService)
    {
        $this->orderService = $orderService;
        $this->outletService = $outletService;
    }

    /**
     * Display the orders master page or fetch JSON list.
     */
    public function index(Request $request)
    {
        $stats = $this->orderService->getSummaryStats();

        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'order_status', 'payment_status', 'outlet_id', 'date_range', 'sort']);
            $perPage = $request->input('per_page', 10);

            $orders = $this->orderService->getPaginatedOrders($filters, $perPage);
            $paginated = new PaginateResource($orders, OrderResource::class);

            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats
            ]);

            return ResponseHelper::jsonResponse(true, 'Data order berhasil diambil', $responseData, 200);
        }

        $outlets = $this->outletService->getAllOutlets();
        $customers = User::role('customer')->orderBy('name')->get();

        return view('pages.master.order', [
            'topbarTitle' => 'Order',
            'topbarIcon' => 'fa-receipt',
            'outlets' => $outlets,
            'customers' => $customers,
            'stats' => $stats
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated());
        return ResponseHelper::jsonResponse(true, 'Order berhasil dibuat', new OrderResource($order), 201);
    }

    /**
     * Display the specified order.
     */
    public function show(string $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        return ResponseHelper::jsonResponse(true, 'Detail order berhasil diambil', new OrderResource($order), 200);
    }

    /**
     * Update the specified order.
     */
    public function update(OrderRequest $request, string $id): JsonResponse
    {
        $order = $this->orderService->updateOrder($id, $request->validated());
        return ResponseHelper::jsonResponse(true, 'Order berhasil diperbarui', new OrderResource($order), 200);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->orderService->deleteOrder($id);
        return ResponseHelper::jsonResponse(true, 'Order berhasil dihapus', null, 200);
    }
}
