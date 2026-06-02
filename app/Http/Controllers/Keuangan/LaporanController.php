<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keuangan\LaporanFilterRequest;
use App\Http\Resources\Keuangan\LaporanResource;
use App\Services\Contracts\LaporanServiceInterface;

class LaporanController extends Controller
{
    protected LaporanServiceInterface $laporanService;

    /**
     * Inject the reports service.
     */
    public function __construct(LaporanServiceInterface $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    /**
     * Display a listing of the reports with dynamic calculation.
     *
     * @param LaporanFilterRequest $request
     * @return \Illuminate\View\View
     */
    public function index(LaporanFilterRequest $request)
    {
        $data = $this->laporanService->getReportsData($request->validated());
        
        $resource = (new LaporanResource($data))->toArray($request);

        return view('pages.keuangan.laporan.index', $resource);
    }
}
