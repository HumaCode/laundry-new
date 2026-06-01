<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\PaginateResource;
use App\Helpers\ResponseHelper;
use App\Services\CustomerService;
use App\Services\OutletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    protected $customerService;
    protected $outletService;

    public function __construct(CustomerService $customerService, OutletService $outletService)
    {
        $this->customerService = $customerService;
        $this->outletService = $outletService;
    }

    /**
     * Display the customers master page or fetch JSON list.
     */
    public function index(Request $request)
    {
        $stats = $this->customerService->getSummaryStats();

        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'tier', 'outlet_id', 'sort']);
            $perPage = $request->input('per_page', 10);
            
            $customers = $this->customerService->getPaginatedCustomers($filters, $perPage);
            $paginated = new PaginateResource($customers, CustomerResource::class);
            
            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats
            ]);
            
            return ResponseHelper::jsonResponse(true, 'Data pelanggan berhasil diambil', $responseData, 200);
        }

        $outlets = $this->outletService->getAllOutlets();

        return view('pages.master.pelanggan', [
            'topbarTitle' => 'Pelanggan',
            'topbarIcon' => 'fa-users',
            'outlets' => $outlets,
            'stats' => $stats
        ]);
    }

    /**
     * Store a newly created customer.
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        // Convert gender translation if needed (e.g. Laki-laki to male, Perempuan to female)
        $validated = $request->validated();
        if (isset($validated['gender'])) {
            if ($validated['gender'] === 'Laki-laki') {
                $validated['gender'] = 'male';
            } elseif ($validated['gender'] === 'Perempuan') {
                $validated['gender'] = 'female';
            }
        }

        $customer = $this->customerService->createCustomer($validated);
        return ResponseHelper::jsonResponse(true, 'Pelanggan berhasil ditambahkan', new CustomerResource($customer), 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(string $id): JsonResponse
    {
        $customer = $this->customerService->getCustomerById($id);
        return ResponseHelper::jsonResponse(true, 'Detail pelanggan berhasil diambil', new CustomerResource($customer), 200);
    }

    /**
     * Update the specified customer.
     */
    public function update(CustomerRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        if (isset($validated['gender'])) {
            if ($validated['gender'] === 'Laki-laki') {
                $validated['gender'] = 'male';
            } elseif ($validated['gender'] === 'Perempuan') {
                $validated['gender'] = 'female';
            }
        }

        $customer = $this->customerService->updateCustomer($id, $validated);
        return ResponseHelper::jsonResponse(true, 'Pelanggan berhasil diperbarui', new CustomerResource($customer), 200);
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->customerService->deleteCustomer($id);
        return ResponseHelper::jsonResponse(true, 'Pelanggan berhasil dihapus', null, 200);
    }
}
