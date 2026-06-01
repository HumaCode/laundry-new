<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Master\Outlet;
use App\Models\Master\Employee;
use App\Models\Operasional\Pickup;

beforeEach(function () {
    // Disable CSRF/Request Forgery checks for all tests in this file
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);

    // Create roles
    Role::updateOrCreate(
        ['slug' => 'customer'],
        ['name' => 'customer', 'slug' => 'customer', 'guard_name' => 'web']
    );
    Role::updateOrCreate(
        ['slug' => 'driver'],
        ['name' => 'driver', 'slug' => 'driver', 'guard_name' => 'web']
    );

    $this->user = User::factory()->create();
    
    // Create outlet
    $this->outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-001',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    // Create a driver / employee
    $this->employee = Employee::create([
        'name' => 'Kurir Budi',
        'code' => 'K001',
        'phone' => '082211223344',
        'address' => 'Jl. Kurir 1',
        'role' => 'driver',
        'is_active' => true,
    ]);
});

test('shuttles page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/shuttles');

    $response->assertStatus(200);
    $response->assertSee('Antar');
});

test('shuttles list can be fetched via ajax', function () {
    Pickup::create([
        'trip_code' => 'TRP-2026-001',
        'customer_name' => 'Budi Santoso',
        'customer_phone' => '08123456789',
        'outlet_id' => $this->outlet->id,
        'address_from' => 'Jl. Mawar 10',
        'address_to' => 'Outlet Test',
        'service_type' => 'Antar Jemput Standar',
        'employee_id' => $this->employee->id,
        'distance' => 5.2,
        'eta' => '20 menit',
        'fee' => 15000,
        'scheduled_at' => now()->addDay(),
        'weight' => '3 kg',
        'notes' => 'Catatan test',
        'status' => 'jemput',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson('/shuttles', [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'data',
            'meta',
            'stats',
        ]
    ]);
});

test('shuttle can be created', function () {
    $payload = [
        'customer_name' => 'Budi Baru',
        'customer_phone' => '085544332211',
        'address_from' => 'Jl. Pahlawan No. 7',
        'address_to' => 'Outlet Test',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
        'outlet_id' => $this->outlet->id,
        'service_type' => 'Antar Jemput Standar',
        'employee_id' => $this->employee->id,
        'weight' => '5 kg',
        'notes' => 'Catatan baru',
        'status' => 'menunggu',
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson('/shuttles', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    
    $this->assertDatabaseHas('pickups', [
        'customer_name' => 'Budi Baru',
        'customer_phone' => '085544332211',
        'address_from' => 'Jl. Pahlawan No. 7',
    ]);
});

test('shuttle details can be retrieved', function () {
    $pickup = Pickup::create([
        'trip_code' => 'TRP-2026-002',
        'customer_name' => 'Siti Detail',
        'customer_phone' => '082233445566',
        'outlet_id' => $this->outlet->id,
        'address_from' => 'Jl. Melati 5',
        'address_to' => 'Outlet Test',
        'service_type' => 'Antar Jemput Standar',
        'scheduled_at' => now()->addDay(),
        'status' => 'menunggu',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson("/shuttles/{$pickup->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.customer_name', 'Siti Detail');
});

test('shuttle can be updated', function () {
    $pickup = Pickup::create([
        'trip_code' => 'TRP-2026-003',
        'customer_name' => 'Dedi Update',
        'customer_phone' => '083344556677',
        'outlet_id' => $this->outlet->id,
        'address_from' => 'Jl. Kamboja 3',
        'address_to' => 'Outlet Test',
        'service_type' => 'Antar Jemput Standar',
        'scheduled_at' => now()->addDay(),
        'status' => 'menunggu',
    ]);

    $payload = [
        'customer_name' => 'Dedi Update Sukses',
        'customer_phone' => '083344556677',
        'address_from' => 'Jl. Kamboja 3',
        'address_to' => 'Outlet Test',
        'service_type' => 'Antar Jemput Standar',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
        'status' => 'jemput',
    ];

    $response = $this
        ->actingAs($this->user)
        ->putJson("/shuttles/{$pickup->id}", $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('pickups', [
        'id' => $pickup->id,
        'customer_name' => 'Dedi Update Sukses',
        'status' => 'jemput',
    ]);
});

test('shuttle can be deleted', function () {
    $pickup = Pickup::create([
        'trip_code' => 'TRP-2026-004',
        'customer_name' => 'Riko Hapus',
        'customer_phone' => '084455667788',
        'outlet_id' => $this->outlet->id,
        'address_from' => 'Jl. Kenanga 12',
        'address_to' => 'Outlet Test',
        'service_type' => 'Antar Jemput Standar',
        'scheduled_at' => now()->addDay(),
        'status' => 'menunggu',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/shuttles/{$pickup->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertSoftDeleted('pickups', [
        'id' => $pickup->id,
    ]);
});
