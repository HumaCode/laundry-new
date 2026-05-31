<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Traits\HasPermission;
use App\Traits\HasMenuPermission;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HasPermission, HasMenuPermission; 
}
