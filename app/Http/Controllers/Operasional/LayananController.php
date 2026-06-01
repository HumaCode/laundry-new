<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Helpers\ResponseHelper;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * Display the services operasional page or fetch JSON list.
     */
    public function index(Request $request)
    {
        $stats = $this->serviceService->getSummaryStats();

        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'category', 'status', 'sort']);
            $perPage = $request->input('per_page', 6);
            
            $services = $this->serviceService->getAllServices($filters, $perPage);
            $paginated = new \App\Http\Resources\PaginateResource($services, ServiceResource::class);
            
            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats
            ]);
            
            return ResponseHelper::jsonResponse(true, 'Data layanan berhasil diambil', $responseData, 200);
        }

        return view('pages.operasional.layanan.index', [
            'topbarTitle' => 'Layanan & Harga',
            'topbarIcon' => 'fa-concierge-bell',
            'stats' => $stats
        ]);
    }

    /**
     * Store a newly created service.
     */
    public function store(ServiceRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Clean features & tiers (remove empty/null array entries)
        if (isset($validated['features'])) {
            $validated['features'] = array_filter($validated['features'], fn($f) => !is_null($f) && trim($f) !== '');
        }
        if (isset($validated['tiers'])) {
            $validated['tiers'] = array_filter($validated['tiers'], fn($t) => isset($t['label']) && trim($t['label']) !== '');
        }

        $service = $this->serviceService->createService($validated);
        return ResponseHelper::jsonResponse(true, 'Layanan berhasil ditambahkan', new ServiceResource($service), 201);
    }

    /**
     * Display the specified service.
     */
    public function show(string $id): JsonResponse
    {
        $service = $this->serviceService->getServiceById($id);
        return ResponseHelper::jsonResponse(true, 'Detail layanan berhasil diambil', new ServiceResource($service), 200);
    }

    /**
     * Update the specified service.
     */
    public function update(ServiceRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        // Clean features & tiers
        if (isset($validated['features'])) {
            $validated['features'] = array_filter($validated['features'], fn($f) => !is_null($f) && trim($f) !== '');
        }
        if (isset($validated['tiers'])) {
            $validated['tiers'] = array_filter($validated['tiers'], fn($t) => isset($t['label']) && trim($t['label']) !== '');
        }

        $service = $this->serviceService->updateService($id, $validated);
        return ResponseHelper::jsonResponse(true, 'Layanan berhasil diperbarui', new ServiceResource($service), 200);
    }

    /**
     * Remove the specified service.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->serviceService->deleteService($id);
        return ResponseHelper::jsonResponse(true, 'Layanan berhasil dihapus', null, 200);
    }

    /**
     * Toggle the status of the specified service.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $service = $this->serviceService->toggleServiceStatus($id);
        return ResponseHelper::jsonResponse(true, 'Status layanan berhasil diperbarui', new ServiceResource($service), 200);
    }

    /**
     * Update service prices in bulk.
     */
    public function bulkPriceUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|in:all,kiloan,satuan,paket,antar',
            'type' => 'required|string|in:up,down',
            'adjustment_type' => 'required|string|in:percentage,nominal',
            'value' => 'required|numeric|min:0',
        ]);

        $count = $this->serviceService->bulkPriceUpdate(
            $validated['category'],
            $validated['type'],
            $validated['adjustment_type'],
            $validated['value']
        );

        return ResponseHelper::jsonResponse(true, "Berhasil memperbarui harga untuk {$count} layanan", null, 200);
    }
}
