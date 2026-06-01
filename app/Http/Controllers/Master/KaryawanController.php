<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PaginateResource;
use App\Helpers\ResponseHelper;
use App\Services\EmployeeService;
use App\Services\OutletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    protected $employeeService;
    protected $outletService;

    public function __construct(EmployeeService $employeeService, OutletService $outletService)
    {
        $this->employeeService = $employeeService;
        $this->outletService = $outletService;
    }

    /**
     * Display the employees master page or fetch JSON list.
     */
    public function index(Request $request)
    {
        $stats = $this->employeeService->getSummaryStats();

        if ($request->wantsJson() || $request->ajax()) {
            $filters = $request->only(['search', 'status', 'outlet_id', 'role', 'sort']);
            $perPage = $request->input('per_page', 10);
            
            $employees = $this->employeeService->getPaginatedEmployees($filters, $perPage);
            $paginated = new PaginateResource($employees, EmployeeResource::class);
            
            $responseData = array_merge($paginated->toArray($request), [
                'stats' => $stats
            ]);
            
            return ResponseHelper::jsonResponse(true, 'Data karyawan berhasil diambil', $responseData, 200);
        }

        $outlets = $this->outletService->getAllOutlets();

        return view('pages.master.karyawan.index', [
            'topbarTitle' => 'Karyawan',
            'topbarIcon' => 'fa-user-check',
            'outlets' => $outlets,
            'stats' => $stats
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->validated());
        return ResponseHelper::jsonResponse(true, 'Karyawan berhasil ditambahkan', new EmployeeResource($employee), 201);
    }

    /**
     * Display the specified employee.
     */
    public function show(string $id): JsonResponse
    {
        $employee = $this->employeeService->getEmployeeById($id);
        return ResponseHelper::jsonResponse(true, 'Detail karyawan berhasil diambil', new EmployeeResource($employee), 200);
    }

    /**
     * Update the specified employee.
     */
    public function update(EmployeeRequest $request, string $id): JsonResponse
    {
        $employee = $this->employeeService->updateEmployee($id, $request->validated());
        return ResponseHelper::jsonResponse(true, 'Karyawan berhasil diperbarui', new EmployeeResource($employee), 200);
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->employeeService->deleteEmployee($id);
        return ResponseHelper::jsonResponse(true, 'Karyawan berhasil dihapus', null, 200);
    }
}
