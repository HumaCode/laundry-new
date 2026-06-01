<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\OutletRequest;
use App\Http\Resources\OutletResource;
use App\Http\Resources\PaginateResource;
use App\Helpers\ResponseHelper;
use App\Services\OutletService;
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
        $stats = $this->outletService->getSummaryStats();

        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'status', 'city', 'sort']);
            $perPage = $request->input('per_page', 10);
            
            $outlets = $this->outletService->getPaginatedOutlets($filters, $perPage);
            
            $paginated = new PaginateResource($outlets, OutletResource::class);
            
            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats
            ]);
            
            return ResponseHelper::jsonResponse(true, 'Data outlet berhasil diambil', $responseData, 200);
        }

        return view('pages.master.outlet.index', [
            'topbarTitle' => 'Outlet',
            'topbarIcon' => 'fa-store',
            'cities' => $stats['cities'],
            'stats' => $stats
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
