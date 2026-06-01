<?php

namespace App\Repositories\Eloquent;

use App\Models\Operasional\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Support\Str;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * Get services list with filters and sorting.
     */
    public function getAll(array $filters, ?int $perPage = null)
    {
        $query = Service::query();
 
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('service_code', 'like', "%{$search}%");
            });
        }
 
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }
 
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN));
        }
 
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'price-asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
 
        if ($perPage) {
            return $query->paginate($perPage);
        }
 
        return $query->get();
    }

    /**
     * Find service by ID.
     */
    public function findById(string $id)
    {
        return Service::findOrFail($id);
    }

    /**
     * Create a new service.
     */
    public function create(array $data)
    {
        if (empty($data['service_code'])) {
            $count = Service::withTrashed()->count();
            $data['service_code'] = 'SVC-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        }
        return Service::create($data);
    }

    /**
     * Update an existing service.
     */
    public function update(string $id, array $data)
    {
        $service = $this->findById($id);
        $service->update($data);
        return $service;
    }

    /**
     * Delete a service.
     */
    public function delete(string $id)
    {
        $service = $this->findById($id);
        return $service->delete();
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        $service = $this->findById($id);
        $service->status = !$service->status;
        $service->save();
        return $service;
    }

    /**
     * Get summary statistics of services.
     */
    public function getSummaryStats()
    {
        $allServices = Service::all();
        $total = $allServices->count();
        $active = $allServices->where('status', true)->count();
        
        $bestSeller = Service::orderBy('orders', 'desc')->first();
        $topRevenue = Service::orderBy('revenue', 'desc')->first();

        return [
            'total' => $total,
            'active' => $active,
            'terlaris' => $bestSeller ? $bestSeller->name : '—',
            'revenue_max' => $topRevenue ? $topRevenue->name : '—',
            'counts' => [
                'all' => $total,
                'kiloan' => $allServices->where('category', 'kiloan')->count(),
                'satuan' => $allServices->where('category', 'satuan')->count(),
                'paket' => $allServices->where('category', 'paket')->count(),
                'antar' => $allServices->where('category', 'antar')->count(),
            ]
        ];
    }

    /**
     * Bulk update service prices.
     */
    public function bulkPriceUpdate(string $category, string $type, string $adjustmentType, float $value)
    {
        $query = Service::query();

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $services = $query->get();
        $count = 0;

        foreach ($services as $service) {
            $originalPrice = $service->price;
            if ($adjustmentType === 'percentage') {
                $adjustment = $originalPrice * ($value / 100);
            } else {
                $adjustment = $value;
            }

            if ($type === 'up') {
                $newPrice = $originalPrice + $adjustment;
            } else {
                $newPrice = max(0, $originalPrice - $adjustment);
            }

            $service->update(['price' => round($newPrice)]);
            $count++;
        }

        return $count;
    }
}
