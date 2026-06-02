<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operasional\Inventory;
use App\Models\Master\Outlet;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;

class InventarisController extends Controller
{
    /**
     * Display the inventory page or fetch JSON list.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Inventory::query()->with('outlet');

            // Search
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($request->filled('category')) {
                $query->where('category', $request->input('category'));
            }

            // Outlet filter
            if ($request->filled('outlet')) {
                $outlet = $request->input('outlet');
                $query->where('outlet_id', $outlet);
            }

            // Status filter
            if ($request->filled('status')) {
                $status = $request->input('status');
                $query->where(function ($q) use ($status) {
                    $this->applyStatusFilter($q, $status);
                });
            }

            // Stat filter (cukup, rendah, kritis)
            if ($request->filled('stat_filter') && $request->input('stat_filter') !== 'all') {
                $stat = $request->input('stat_filter');
                $query->where(function ($q) use ($stat) {
                    $this->applyStatusFilter($q, $stat);
                });
            }

            // Sort
            $sortCol = $request->input('sort_col', 'name');
            $sortDir = $request->input('sort_dir', 'asc');

            // Map column names from JS names to DB names if different
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

            $inventories = $query->get();

            // Format items to match JS
            $items = $inventories->map(function ($item) {
                $stock = $item->stock;
                $minS = $item->min_stock;
                $maxS = $item->max_stock;
                $pct = $maxS > 0 ? ($stock / $maxS) : 0;

                if ($stock === 0) {
                    $status = 'habis';
                } elseif ($stock < $minS * 0.5) {
                    $status = 'kritis';
                } elseif ($stock < $minS) {
                    $status = 'rendah';
                } elseif ($pct > 0.8) {
                    $status = 'lebih';
                } else {
                    $status = 'cukup';
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'brand' => $item->brand,
                    'category' => $item->category,
                    'emoji' => $item->emoji ?? '📦',
                    'color' => $item->color ?? '#6366F1',
                    'stock' => $item->stock,
                    'minStock' => $item->min_stock,
                    'maxStock' => $item->max_stock,
                    'unit' => $item->unit,
                    'price' => $item->price,
                    'value' => $item->stock * $item->price,
                    'status' => $status,
                    'outlet' => $item->outlet->name ?? '—',
                    'outlet_id' => $item->outlet_id,
                    'desc' => $item->desc,
                    'lastRestock' => $item->last_restock ? $item->last_restock->format('Y-m-d') : null,
                    'lastRestockQty' => $item->last_restock_qty,
                    'history' => $item->history ?? [],
                ];
            });

            // Calculate overall stats based on all database items
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
                $maxS = $item->max_stock;

                if ($stock === 0 || $stock < $minS * 0.5) {
                    $stats['kritis']++;
                } elseif ($stock < $minS) {
                    $stats['rendah']++;
                } else {
                    $stats['cukup']++;
                }
                $stats['total_value'] += ($stock * $item->price);
            }

            return ResponseHelper::jsonResponse(true, 'Data inventaris berhasil diambil', [
                'items' => $items,
                'stats' => $stats
            ], 200);
        }

        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        return view('pages.operasional.inventaris.index', [
            'topbarTitle' => 'Inventaris',
            'topbarIcon' => 'fa-boxes',
            'outlets' => $outlets
        ]);
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:inventories,code',
            'brand' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'emoji' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:10',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'outlet_id' => 'required|exists:outlets,id',
            'desc' => 'nullable|string',
        ]);

        $validated['history'] = [];

        $item = Inventory::create($validated);

        return ResponseHelper::jsonResponse(true, 'Barang berhasil ditambahkan', $item, 201);
    }

    /**
     * Show details of a specific inventory item.
     */
    public function show(string $id): JsonResponse
    {
        $item = Inventory::with('outlet')->findOrFail($id);

        $stock = $item->stock;
        $minS = $item->min_stock;
        $maxS = $item->max_stock;
        $pct = $maxS > 0 ? ($stock / $maxS) : 0;

        if ($stock === 0) {
            $status = 'habis';
        } elseif ($stock < $minS * 0.5) {
            $status = 'kritis';
        } elseif ($stock < $minS) {
            $status = 'rendah';
        } elseif ($pct > 0.8) {
            $status = 'lebih';
        } else {
            $status = 'cukup';
        }

        $formatted = [
            'id' => $item->id,
            'name' => $item->name,
            'code' => $item->code,
            'brand' => $item->brand,
            'category' => $item->category,
            'emoji' => $item->emoji ?? '📦',
            'color' => $item->color ?? '#6366F1',
            'stock' => $item->stock,
            'minStock' => $item->min_stock,
            'maxStock' => $item->max_stock,
            'unit' => $item->unit,
            'price' => $item->price,
            'value' => $item->stock * $item->price,
            'status' => $status,
            'outlet' => $item->outlet->name ?? '—',
            'outlet_id' => $item->outlet_id,
            'desc' => $item->desc,
            'lastRestock' => $item->last_restock ? $item->last_restock->format('Y-m-d') : null,
            'lastRestockQty' => $item->last_restock_qty,
            'history' => $item->history ?? [],
        ];

        return ResponseHelper::jsonResponse(true, 'Detail barang berhasil diambil', $formatted, 200);
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $item = Inventory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:inventories,code,' . $id . ',id',
            'brand' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'emoji' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:10',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'outlet_id' => 'required|exists:outlets,id',
            'desc' => 'nullable|string',
        ]);

        $item->update($validated);

        return ResponseHelper::jsonResponse(true, 'Barang berhasil diperbarui', $item, 200);
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(string $id): JsonResponse
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return ResponseHelper::jsonResponse(true, 'Barang berhasil dihapus', null, 200);
    }

    /**
     * Restock a specific inventory item.
     */
    public function restock(Request $request, string $id): JsonResponse
    {
        $item = Inventory::findOrFail($id);

        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'invoice' => 'nullable|string|max:100',
            'price' => 'nullable|integer|min:0',
            'date' => 'required|date',
        ]);

        $newStock = $item->stock + $validated['qty'];

        // Update history array
        $history = $item->history ?? [];
        array_unshift($history, [
            'date' => $validated['date'],
            'qty' => (int) $validated['qty'],
            'supplier' => $validated['supplier'] ?? '—',
            'invoice' => $validated['invoice'] ?? '—',
        ]);

        // Keep last 15 history items to save space
        if (count($history) > 15) {
            $history = array_slice($history, 0, 15);
        }

        $updateData = [
            'stock' => $newStock,
            'last_restock' => $validated['date'],
            'last_restock_qty' => $validated['qty'],
            'history' => $history,
        ];

        if ($request->filled('price')) {
            $updateData['price'] = $validated['price'];
        }

        $item->update($updateData);

        return ResponseHelper::jsonResponse(true, 'Restock barang berhasil dilakukan', $item, 200);
    }

    /**
     * Apply status filter conditions.
     */
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
