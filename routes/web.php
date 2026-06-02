<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\OutletController;
use App\Http\Controllers\Master\KaryawanController;
use App\Http\Controllers\Master\BisnisController;
use App\Http\Controllers\Master\OrderController;
use App\Http\Controllers\Keuangan\LaporanController;
use App\Http\Controllers\Keuangan\PembayaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Pelanggan (Customers)
    Route::get('/customers', [PelangganController::class, 'index'])->name('customers');
    Route::post('/customers', [PelangganController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}', [PelangganController::class, 'show'])->name('customers.show');
    Route::put('/customers/{id}', [PelangganController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [PelangganController::class, 'destroy'])->name('customers.destroy');

    // Order (Orders)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Outlet
    Route::get('/outlets', [OutletController::class, 'index'])->name('outlets');
    Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
    Route::get('/outlets/{id}', [OutletController::class, 'show'])->name('outlets.show');
    Route::put('/outlets/{id}', [OutletController::class, 'update'])->name('outlets.update');
    Route::delete('/outlets/{id}', [OutletController::class, 'destroy'])->name('outlets.destroy');

    // Karyawan (Employees)
    Route::get('/employees', [KaryawanController::class, 'index'])->name('employees');
    Route::post('/employees', [KaryawanController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}', [KaryawanController::class, 'show'])->name('employees.show');
    Route::put('/employees/{id}', [KaryawanController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [KaryawanController::class, 'destroy'])->name('employees.destroy');

    // Bisnis (Businesses)
    Route::get('/businesses', [BisnisController::class, 'index'])->name('businesses');
    Route::post('/businesses', [BisnisController::class, 'store'])->name('businesses.store');
    Route::get('/businesses/{id}', [BisnisController::class, 'show'])->name('businesses.show');
    Route::put('/businesses/{id}', [BisnisController::class, 'update'])->name('businesses.update');
    Route::delete('/businesses/{id}', [BisnisController::class, 'destroy'])->name('businesses.destroy');

    // Layanan & Harga (Services)
    Route::get('/services', [\App\Http\Controllers\Operasional\LayananController::class, 'index'])->name('services');
    Route::post('/services', [\App\Http\Controllers\Operasional\LayananController::class, 'store'])->name('services.store');
    Route::get('/services/{id}', [\App\Http\Controllers\Operasional\LayananController::class, 'show'])->name('services.show');
    Route::put('/services/{id}', [\App\Http\Controllers\Operasional\LayananController::class, 'update'])->name('services.update');
    Route::delete('/services/{id}', [\App\Http\Controllers\Operasional\LayananController::class, 'destroy'])->name('services.destroy');
    Route::patch('/services/{id}/toggle-status', [\App\Http\Controllers\Operasional\LayananController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::post('/services/bulk-price', [\App\Http\Controllers\Operasional\LayananController::class, 'bulkPriceUpdate'])->name('services.bulk-price');

    // Antar Jemput (Pickup & Delivery)
    Route::get('/shuttles', [\App\Http\Controllers\Operasional\AntarJemputController::class, 'index'])->name('shuttles');
    Route::post('/shuttles', [\App\Http\Controllers\Operasional\AntarJemputController::class, 'store'])->name('shuttles.store');
    Route::get('/shuttles/{id}', [\App\Http\Controllers\Operasional\AntarJemputController::class, 'show'])->name('shuttles.show');
    Route::put('/shuttles/{id}', [\App\Http\Controllers\Operasional\AntarJemputController::class, 'update'])->name('shuttles.update');
    Route::delete('/shuttles/{id}', [\App\Http\Controllers\Operasional\AntarJemputController::class, 'destroy'])->name('shuttles.destroy');

    // Inventaris (Inventory)
    Route::get('/inventories', [\App\Http\Controllers\Operasional\InventarisController::class, 'index'])->name('inventories');
    Route::post('/inventories', [\App\Http\Controllers\Operasional\InventarisController::class, 'store'])->name('inventories.store');
    Route::post('/inventories/auto-restock', [\App\Http\Controllers\Operasional\InventarisController::class, 'autoRestock'])->name('inventories.auto-restock');
    Route::get('/inventories/{id}', [\App\Http\Controllers\Operasional\InventarisController::class, 'show'])->name('inventories.show');
    Route::put('/inventories/{id}', [\App\Http\Controllers\Operasional\InventarisController::class, 'update'])->name('inventories.update');
    Route::delete('/inventories/{id}', [\App\Http\Controllers\Operasional\InventarisController::class, 'destroy'])->name('inventories.destroy');
    Route::post('/inventories/{id}/restock', [\App\Http\Controllers\Operasional\InventarisController::class, 'restock'])->name('inventories.restock');

    // Laporan (Reports)
    Route::get('/reports', [LaporanController::class, 'index'])->name('reports');

    // Pembayaran (Payments)
    Route::get('/payments', [PembayaranController::class, 'index'])->name('payments');
    Route::get('/payments/{id}', [PembayaranController::class, 'show'])->name('payments.show');
    Route::put('/payments/{id}', [PembayaranController::class, 'update'])->name('payments.update');
});

require __DIR__.'/auth.php';
