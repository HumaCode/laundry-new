<?php

namespace App\Repositories\Eloquent;

use App\Models\Operasional\Inventory;
use App\Repositories\Contracts\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function getFiltered(array $filters)
    {
        $query = Inventory::query()->with('outlet');

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Category filter
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Outlet filter
        if (!empty($filters['outlet'])) {
            $query->where('outlet_id', $filters['outlet']);
        }

        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where(function ($q) use ($filters) {
                $this->applyStatusFilter($q, $filters['status']);
            });
        }

        // Stat filter (cukup, rendah, kritis)
        if (!empty($filters['stat_filter']) && $filters['stat_filter'] !== 'all') {
            $query->where(function ($q) use ($filters) {
                $this->applyStatusFilter($q, $filters['stat_filter']);
            });
        }

        // Sort
        $sortCol = $filters['sort_col'] ?? 'name';
        $sortDir = $filters['sort_dir'] ?? 'asc';

        $sortMap = [
            'name' => 'name',
            'category' => 'category',
            'stock' => 'stock',
            'price' => 'price',
            'lastRestock' => 'last_restock',
        ];

        $dbCol = $sortMap[$sortCol] ?? 'name';

        if ($sortCol === 'outlet') {
            $query->join('outlets', 'inventories.outlet_id', '=', 'outlets.id')
                ->orderBy('outlets.name', $sortDir)
                ->select('inventories.*');
        } else if ($sortCol === 'value') {
            $query->orderByRaw('stock * price ' . $sortDir);
        } else if ($sortCol === 'stockStatus') {
            $query->orderByRaw('(stock / CAST(min_stock AS DECIMAL)) ' . $sortDir);
        } else {
            $query->orderBy($dbCol, $sortDir);
        }

        return $query->get();
    }

    public function getSummaryStats()
    {
        $allInventories = Inventory::all();
        $stats = [
            'total_items' => $allInventories->count(),
            'cukup' => 0,
            'rendah' => 0,
            'kritis' => 0,
            'total_value' => 0,
        ];

        foreach ($allInventories as $item) {
            $stock = $item->stock;
            $minS = $item->min_stock;

            if ($stock === 0 || $stock < $minS * 0.5) {
                $stats['kritis']++;
            } elseif ($stock < $minS) {
                $stats['rendah']++;
            } else {
                $stats['cukup']++;
            }
            $stats['total_value'] += ($stock * $item->price);
        }

        return $stats;
    }

    public function findById(string $id)
    {
        return Inventory::with('outlet')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Inventory::create($data);
    }

    public function update(string $id, array $data)
    {
        $item = $this->findById($id);
        $item->update($data);
        return $item;
    }

    public function delete(string $id)
    {
        $item = $this->findById($id);
        return $item->delete();
    }

    public function getBelowMinStock()
    {
        return Inventory::whereRaw('stock < min_stock')->get();
    }

    private function applyStatusFilter($q, $status)
    {
        if ($status === 'habis') {
            $q->where('stock', 0);
        } elseif ($status === 'kritis') {
            $q->where('stock', '>', 0)->whereRaw('stock < min_stock * 0.5');
        } elseif ($status === 'rendah') {
            $q->whereRaw('stock >= min_stock * 0.5')->whereRaw('stock < min_stock');
        } elseif ($status === 'lebih') {
            $q->whereRaw('stock > max_stock * 0.8');
        } elseif ($status === 'cukup') {
            $q->whereRaw('stock >= min_stock')->whereRaw('stock <= max_stock * 0.8');
        }
    }
}
