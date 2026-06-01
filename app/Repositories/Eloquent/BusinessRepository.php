<?php

namespace App\Repositories\Eloquent;

use App\Models\Master\Business;
use App\Repositories\Contracts\BusinessRepositoryInterface;

class BusinessRepository implements BusinessRepositoryInterface
{
    /**
     * Get paginated businesses with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage)
    {
        $query = Business::withCount('outlets');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('owner', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'Aktif') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'Tidak Aktif') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'outlets-desc':
                    $query->orderByDesc('outlets_count');
                    break;
                case 'recent':
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
     * Find business by ID.
     */
    public function findById(string $id)
    {
        return Business::with(['outlets.employees'])->findOrFail($id);
    }

    /**
     * Create a new business.
     */
    public function create(array $data)
    {
        return Business::create($data);
    }

    /**
     * Update an existing business.
     */
    public function update(string $id, array $data)
    {
        $business = $this->findById($id);
        $business->update($data);
        return $business;
    }

    /**
     * Delete a business.
     */
    public function delete(string $id)
    {
        $business = $this->findById($id);
        return $business->delete();
    }

    /**
     * Get summary statistics of businesses.
     */
    public function getSummaryStats()
    {
        $all        = Business::withCount('outlets')->get();
        $total      = $all->count();
        $active     = $all->where('is_active', true)->count();
        $inactive   = $all->where('is_active', false)->count();
        $totalOutlets = $all->sum('outlets_count');

        $activePercentage = $total > 0 ? round(($active / $total) * 100) : 0;

        $cities = $all->pluck('city')
            ->filter()
            ->unique(fn($c) => strtolower(trim($c)))
            ->values()
            ->toArray();

        return [
            'total_businesses'   => $total,
            'active_businesses'  => $active,
            'inactive_businesses'=> $inactive,
            'total_outlets'      => $totalOutlets,
            'active_percentage'  => $activePercentage,
            'cities'             => $cities,
            'cities_count'       => count($cities),
        ];
    }

    /**
     * Get all businesses for dropdowns.
     */
    public function getAll()
    {
        return Business::where('is_active', true)->orderBy('name')->get();
    }
}
