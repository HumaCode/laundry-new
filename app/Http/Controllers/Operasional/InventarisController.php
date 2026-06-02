<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Requests\RestockInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Services\InventoryService;
use App\Repositories\Contracts\OutletRepositoryInterface;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InventarisController extends Controller
{
    protected $inventoryService;
    protected $outletRepository;

    public function __construct(
        InventoryService $inventoryService,
        OutletRepositoryInterface $outletRepository
    ) {
        $this->inventoryService = $inventoryService;
        $this->outletRepository = $outletRepository;
    }

    /**
     * Display the inventory page or fetch JSON list.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $inventories = $this->inventoryService->getFilteredInventories($request->all());
            $items = InventoryResource::collection($inventories);
            $stats = $this->inventoryService->getSummaryStats();

            return ResponseHelper::jsonResponse(true, 'Data inventaris berhasil diambil', [
                'items' => $items,
                'stats' => $stats
            ], 200);
        }

        $outlets = $this->outletRepository->getAll()->where('is_active', true);

        return view('pages.operasional.inventaris.index', [
            'topbarTitle' => 'Inventaris',
            'topbarIcon' => 'fa-boxes',
            'outlets' => $outlets
        ]);
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(StoreInventoryRequest $request): JsonResponse
    {
        $item = $this->inventoryService->createInventory($request->validated());

        return ResponseHelper::jsonResponse(true, 'Barang berhasil ditambahkan', new InventoryResource($item), 201);
    }

    /**
     * Show details of a specific inventory item.
     */
    public function show(string $id): JsonResponse
    {
        $item = $this->inventoryService->getInventoryById($id);

        return ResponseHelper::jsonResponse(true, 'Detail barang berhasil diambil', new InventoryResource($item), 200);
    }

    /**
     * Update the specified inventory item.
     */
    public function update(UpdateInventoryRequest $request, string $id): JsonResponse
    {
        $item = $this->inventoryService->updateInventory($id, $request->validated());

        return ResponseHelper::jsonResponse(true, 'Barang berhasil diperbarui', new InventoryResource($item), 200);
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->inventoryService->deleteInventory($id);

        return ResponseHelper::jsonResponse(true, 'Barang berhasil dihapus', null, 200);
    }

    /**
     * Restock a specific inventory item.
     */
    public function restock(RestockInventoryRequest $request, string $id): JsonResponse
    {
        $item = $this->inventoryService->restockInventory($id, $request->validated());

        return ResponseHelper::jsonResponse(true, 'Restock barang berhasil dilakukan', new InventoryResource($item), 200);
    }

    /**
     * Automatically restock all items below minimum stock.
     */
    public function autoRestock(Request $request): JsonResponse
    {
        $filters = $request->only(['outlet', 'category', 'search']);

        if (empty($filters['outlet'])) {
            return ResponseHelper::jsonResponse(false, 'Silakan pilih outlet terlebih dahulu sebelum melakukan restock otomatis.', null, 400);
        }

        $count = $this->inventoryService->autoRestock($filters);

        if ($count > 0) {
            return ResponseHelper::jsonResponse(true, $count . ' barang berhasil di-restock otomatis', null, 200);
        }

        return ResponseHelper::jsonResponse(true, 'Semua stok barang pada outlet/filter ini masih aman', null, 200);
    }
}
