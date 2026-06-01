<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickupRequest;
use App\Http\Resources\PickupResource;
use App\Helpers\ResponseHelper;
use App\Services\PickupService;
use App\Models\Master\Outlet;
use App\Models\Master\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AntarJemputController extends Controller
{
    protected $pickupService;

    public function __construct(PickupService $pickupService)
    {
        $this->pickupService = $pickupService;
    }

    /**
     * Display the pickup & delivery page or fetch JSON list.
     */
    public function index(Request $request)
    {
        $stats = $this->pickupService->getSummaryStats();

        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'status', 'outlet_id', 'employee_id', 'date']);
            $perPage = $request->input('per_page', 6);
            
            $pickups = $this->pickupService->getPaginatedPickups($filters, $perPage);
            $paginated = new \App\Http\Resources\PaginateResource($pickups, PickupResource::class);
            
            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats
            ]);
            
            return ResponseHelper::jsonResponse(true, 'Data antar jemput berhasil diambil', $responseData, 200);
        }

        // Fetch support data for filters and creation forms
        $outlets = Outlet::all();
        $drivers = Employee::where('role', 'Kurir')->where('is_active', true)->get();
        $customers = User::role('customer')->get();

        return view('pages.operasional.antarjemput.index', [
            'topbarTitle' => 'Antar & Jemput',
            'topbarIcon' => 'fa-truck',
            'stats' => $stats,
            'outlets' => $outlets,
            'drivers' => $drivers,
            'customers' => $customers
        ]);
    }

    /**
     * Store a newly created pickup.
     */
    public function store(PickupRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Generate automatic distance, eta, fee if not provided
        if (empty($validated['distance'])) {
            $validated['distance'] = rand(2, 10);
        }
        if (empty($validated['eta'])) {
            $validated['eta'] = rand(15, 45) . ' menit';
        }
        if (empty($validated['fee'])) {
            $validated['fee'] = 10000;
        }
        if (empty($validated['avatar_color'])) {
            $colors = ['#6366F1', '#10B981', '#F59E0B', '#EC4899', '#3B82F6', '#8B5CF6', '#F97316'];
            $validated['avatar_color'] = $colors[array_rand($colors)];
        }

        $pickup = $this->pickupService->createPickup($validated);
        return ResponseHelper::jsonResponse(true, 'Trip berhasil ditambahkan', new PickupResource($pickup), 201);
    }

    /**
     * Display the specified pickup.
     */
    public function show(string $id): JsonResponse
    {
        $pickup = $this->pickupService->getPickupById($id);
        return ResponseHelper::jsonResponse(true, 'Detail trip berhasil diambil', new PickupResource($pickup), 200);
    }

    /**
     * Update the specified pickup.
     */
    public function update(PickupRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        $pickup = $this->pickupService->updatePickup($id, $validated);
        return ResponseHelper::jsonResponse(true, 'Trip berhasil diperbarui', new PickupResource($pickup), 200);
    }

    /**
     * Remove the specified pickup.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->pickupService->deletePickup($id);
        return ResponseHelper::jsonResponse(true, 'Trip berhasil dihapus', null, 200);
    }
}
