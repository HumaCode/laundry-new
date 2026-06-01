<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessRequest;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\PaginateResource;
use App\Helpers\ResponseHelper;
use App\Services\BusinessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BisnisController extends Controller
{
    protected $businessService;

    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     * Display the businesses master page or fetch JSON list.
     */
    public function index(Request $request)
    {
        $stats = $this->businessService->getSummaryStats();

        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'status', 'city', 'sort']);
            $perPage = $request->input('per_page', 10);

            $businesses = $this->businessService->getPaginatedBusinesses($filters, $perPage);
            $paginated  = new PaginateResource($businesses, BusinessResource::class);

            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats,
            ]);

            return ResponseHelper::jsonResponse(true, 'Data bisnis berhasil diambil', $responseData, 200);
        }

        return view('pages.master.bisnis.index', [
            'topbarTitle' => 'Bisnis',
            'topbarIcon'  => 'fa-building',
            'stats'       => $stats,
        ]);
    }

    /**
     * Store a newly created business.
     */
    public function store(BusinessRequest $request): JsonResponse
    {
        $business = $this->businessService->createBusiness($request->validated());
        return ResponseHelper::jsonResponse(true, 'Bisnis berhasil ditambahkan', new BusinessResource($business), 201);
    }

    /**
     * Display the specified business.
     */
    public function show(string $id): JsonResponse
    {
        $business = $this->businessService->getBusinessById($id);
        return ResponseHelper::jsonResponse(true, 'Detail bisnis berhasil diambil', new BusinessResource($business), 200);
    }

    /**
     * Update the specified business.
     */
    public function update(BusinessRequest $request, string $id): JsonResponse
    {
        $business = $this->businessService->updateBusiness($id, $request->validated());
        return ResponseHelper::jsonResponse(true, 'Bisnis berhasil diperbarui', new BusinessResource($business), 200);
    }

    /**
     * Remove the specified business.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->businessService->deleteBusiness($id);
        return ResponseHelper::jsonResponse(true, 'Bisnis berhasil dihapus', null, 200);
    }
}
