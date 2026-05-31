<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    /**
     * Display the customers master page.
     */
    public function index(Request $request): View
    {
        return view('pages.master.pelanggan', [
            'topbarTitle' => 'Pelanggan',
            'topbarIcon' => 'fa-users'
        ]);
    }
}
