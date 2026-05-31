<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\OutletController;
use App\Http\Controllers\Master\KaryawanController;
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
});

require __DIR__.'/auth.php';
