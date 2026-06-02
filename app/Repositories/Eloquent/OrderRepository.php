<?php

namespace App\Repositories\Eloquent;

use App\Models\Master\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Get paginated orders with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage)
    {
        $query = Order::with(['customer', 'outlet']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('service_type', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('outlet', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['order_status'])) {
            $query->where('order_status', $filters['order_status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        }

        if (!empty($filters['date_range'])) {
            $dates = explode(' - ', $filters['date_range']);
            if (count($dates) === 2) {
                $startDate = Carbon::parse($dates[0])->startOfDay();
                $endDate = Carbon::parse($dates[1])->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'price-high':
                    $query->orderBy('total_price', 'desc');
                    break;
                case 'price-low':
                    $query->orderBy('total_price', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Find order by ID.
     */
    public function findById(string $id)
    {
        return Order::with(['customer', 'outlet'])->findOrFail($id);
    }

    /**
     * Create a new order.
     */
    public function create(array $data)
    {
        if (empty($data['order_code'])) {
            $data['order_code'] = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        // Calculate total price if not provided
        if (!isset($data['total_price']) || $data['total_price'] == 0) {
            $data['total_price'] = ($data['weight'] ?? 0.0) * ($data['price_per_unit'] ?? 0);
        }

        return Order::create($data);
    }

    /**
     * Update an existing order.
     */
    public function update(string $id, array $data)
    {
        $order = $this->findById($id);

        if (isset($data['weight']) || isset($data['price_per_unit'])) {
            $weight = $data['weight'] ?? $order->weight;
            $pricePerUnit = $data['price_per_unit'] ?? $order->price_per_unit;
            $data['total_price'] = $weight * $pricePerUnit;
        }

        if (isset($data['order_status']) && $data['order_status'] === 'Diambil' && empty($order->finished_at)) {
            $data['finished_at'] = Carbon::now();
        }

        $order->update($data);
        return $order;
    }

    /**
     * Delete an order.
     */
    public function delete(string $id)
    {
        $order = $this->findById($id);
        return $order->delete();
    }

    public function getSummaryStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalOrders = Order::count();
        $baruOrders = Order::where('order_status', 'Baru')->count();
        $prosesOrders = Order::where('order_status', 'Proses')->count();
        $selesaiOrders = Order::where('order_status', 'Selesai')->count();
        $diambilOrders = Order::where('order_status', 'Diambil')->count();
        
        $monthlyRevenue = Order::where('payment_status', 'Lunas')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_price');

        return [
            'total_orders' => $totalOrders,
            'baru_orders' => $baruOrders,
            'proses_orders' => $prosesOrders,
            'selesai_orders' => $selesaiOrders,
            'diambil_orders' => $diambilOrders,
            'processing_orders' => $baruOrders + $prosesOrders + $selesaiOrders,
            'completed_orders' => $diambilOrders,
            'monthly_revenue' => (int) $monthlyRevenue,
        ];
    }
}
