<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    /**
     * Display the inventory page.
     */
    public function index(Request $request)
    {
        return view('pages.operasional.inventaris.index', [
            'topbarTitle' => 'Inventaris',
            'topbarIcon' => 'fa-boxes'
        ]);
    }
}
