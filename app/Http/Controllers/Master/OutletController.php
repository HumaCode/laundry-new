<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutletController extends Controller
{
    /**
     * Display the outlets master page.
     */
    public function index(Request $request): View
    {
        return view('pages.master.outlet', [
            'topbarTitle' => 'Outlet',
            'topbarIcon' => 'fa-store'
        ]);
    }
}
