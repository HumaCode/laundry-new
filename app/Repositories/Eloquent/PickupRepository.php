<?php

namespace App\Repositories\Eloquent;

use App\Models\Operasional\Pickup;
use App\Repositories\Contracts\PickupRepositoryInterface;

class PickupRepository implements PickupRepositoryInterface
{
    /**
     * Get paginated pickups with filters.
     */
    public function getPaginated(array $filters, int $perPage)
    {
        $query = Pickup::with(['customer', 'outlet', 'driver']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('trip_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('address_from', 'like', "%{$search}%")
                  ->orWhere('address_to', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('scheduled_at', $filters['date']);
        }

        $query->orderBy('scheduled_at', 'desc')->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get summary stats of pickups.
     */
    public function getSummaryStats()
    {
        $today = now()->toDateString();
        $allPickups = Pickup::all();
        $todayPickups = Pickup::whereDate('scheduled_at', $today)->get();

        return [
            'all' => $todayPickups->count(),
            'menunggu' => $todayPickups->where('status', 'menunggu')->count(),
            'jemput' => $todayPickups->where('status', 'jemput')->count(),
            'proses' => $todayPickups->where('status', 'proses')->count(),
            'antar' => $todayPickups->where('status', 'antar')->count(),
            'total_all_time' => $allPickups->count(),
        ];
    }

    /**
     * Find pickup by ID.
     */
    public function findById(string $id)
    {
        return Pickup::with(['customer', 'outlet', 'driver'])->findOrFail($id);
    }

    /**
     * Create a new pickup.
     */
    public function create(array $data)
    {
        if (empty($data['trip_code'])) {
            $count = Pickup::withTrashed()->count();
            $data['trip_code'] = 'TRP-' . date('Y') . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        }
        return Pickup::create($data);
    }

    /**
     * Update an existing pickup.
     */
    public function update(string $id, array $data)
    {
        $pickup = $this->findById($id);
        $pickup->update($data);
        return $pickup;
    }

    /**
     * Delete a pickup.
     */
    public function delete(string $id)
    {
        $pickup = $this->findById($id);
        return $pickup->delete();
    }
}
