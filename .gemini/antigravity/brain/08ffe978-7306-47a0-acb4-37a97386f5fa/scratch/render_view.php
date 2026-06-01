<?php

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Boot Laravel
require 'c:/laragon/www/laundry-app/vendor/autoload.php';
$app = require_once 'c:/laragon/www/laundry-app/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get admin user
$admin = User::where('username', 'admin')->first();
if ($admin) {
    Auth::login($admin);
}

// Render the view with mock stats
$stats = [
    'total_businesses' => 5,
    'active_businesses' => 4,
    'inactive_businesses' => 1,
    'total_outlets' => 12,
    'active_percentage' => 80,
    'cities' => ['Jakarta', 'Bandung'],
    'cities_count' => 2,
];

try {
    $html = view('pages.master.bisnis', [
        'topbarTitle' => 'Bisnis',
        'topbarIcon'  => 'fa-building',
        'stats'       => $stats,
    ])->render();
    
    file_put_contents('c:/laragon/www/laundry-app/.gemini/antigravity/brain/08ffe978-7306-47a0-acb4-37a97386f5fa/scratch/rendered_bisnis.html', $html);
    echo "Successfully rendered view to rendered_bisnis.html\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
