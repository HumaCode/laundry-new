<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Master\Outlet;
use App\Models\Master\Order;
use Carbon\Carbon;

beforeEach(function () {
    // Create necessary roles
    Role::updateOrCreate(
        ['slug' => 'customer'],
        ['name' => 'customer', 'slug' => 'customer', 'guard_name' => 'web']
    );

    $this->user = User::factory()->create();
});

test('reports page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/reports');

    $response->assertStatus(200);
    $response->assertSee('Laporan Bisnis');
    $response->assertSee('Total Pendapatan');
});

test('reports page handles outlet and date filters', function () {
    $outlet = Outlet::create([
        'name' => 'Outlet Test 1',
        'code' => 'OT-T01',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->get('/reports?dateFrom=' . Carbon::now()->startOfMonth()->format('Y-m-d') . 
             '&dateTo=' . Carbon::now()->format('Y-m-d') . 
             '&outletSelect=' . $outlet->id);

    $response->assertStatus(200);
    $response->assertSee('Laporan Bisnis');
});
