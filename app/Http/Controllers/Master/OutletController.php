<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\OutletRequest;
use App\Http\Resources\OutletResource;
use App\Http\Resources\PaginateResource;
use App\Helpers\ResponseHelper;
use App\Services\OutletService;
use App\Models\Master\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    protected $outletService;

    public function __construct(OutletService $outletService)
    {
        $this->outletService = $outletService;
    }

    /**
     * Display the outlets master page or fetch JSON list.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'status', 'city', 'sort']);
            $perPage = $request->input('per_page', 10);
            
            $outlets = $this->outletService->getPaginatedOutlets($filters, $perPage);
            
            $paginated = new PaginateResource($outlets, OutletResource::class);
            return ResponseHelper::jsonResponse(true, 'Data outlet berhasil diambil', $paginated, 200);
        }

        $cities = Outlet::whereNotNull('city')
            ->where('city', '!=', '')
            ->orderBy('city', 'asc')
            ->pluck('city')
            ->unique(function ($item) {
                return strtolower(trim($item));
            })
            ->values()
            ->toArray();

        return view('pages.master.outlet', [
            'topbarTitle' => 'Outlet',
            'topbarIcon' => 'fa-store',
            'cities' => $cities
        ]);
    }

    /**
     * Store a newly created outlet.
     */
    public function store(OutletRequest $request): JsonResponse
    {
        $outlet = $this->outletService->createOutlet($request->validated());
        return ResponseHelper::jsonResponse(true, 'Outlet berhasil ditambahkan', new OutletResource($outlet), 201);
    }

    /**
     * Display the specified outlet.
     */
    public function show(string $id): JsonResponse
    {
        $outlet = $this->outletService->getOutletById($id);
        return ResponseHelper::jsonResponse(true, 'Detail outlet berhasil diambil', new OutletResource($outlet), 200);
    }

    /**
     * Update the specified outlet.
     */
    public function update(OutletRequest $request, string $id): JsonResponse
    {
        $outlet = $this->outletService->updateOutlet($id, $request->validated());
        return ResponseHelper::jsonResponse(true, 'Outlet berhasil diperbarui', new OutletResource($outlet), 200);
    }

    /**
     * Remove the specified outlet.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->outletService->deleteOutlet($id);
        return ResponseHelper::jsonResponse(true, 'Outlet berhasil dihapus', null, 200);
    }
}
