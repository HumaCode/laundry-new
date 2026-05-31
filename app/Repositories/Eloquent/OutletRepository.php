<?php

namespace App\Repositories\Eloquent;

use App\Models\Master\Outlet;
use App\Repositories\Contracts\OutletRepositoryInterface;

class OutletRepository implements OutletRepositoryInterface
{
    /**
     * Get paginated outlets with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage)
    {
        $query = Outlet::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('manager', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'Aktif') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'Tutup') {
                $query->where('is_active', false);
            }
            // Maintenance status can be simulated/added as needed
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

        return $query->paginate($perPage);
    }

    /**
     * Find outlet by ID.
     */
    public function findById(string $id)
    {
        return Outlet::findOrFail($id);
    }

    /**
     * Create a new outlet.
     */
    public function create(array $data)
    {
        return Outlet::create($data);
    }

    /**
     * Update an existing outlet.
     */
    public function update(string $id, array $data)
    {
        $outlet = $this->findById($id);
        $outlet->update($data);
        return $outlet;
    }

    /**
     * Delete an outlet.
     */
    public function delete(string $id)
    {
        $outlet = $this->findById($id);
        return $outlet->delete();
    }
}
