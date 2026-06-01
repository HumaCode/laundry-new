<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Master\Outlet;

beforeEach(function () {
    // Create necessary roles
    Role::updateOrCreate(
        ['slug' => 'customer'],
        ['name' => 'customer', 'slug' => 'customer', 'guard_name' => 'web']
    );

    $this->user = User::factory()->create();
});

test('customers page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/customers');

    $response->assertStatus(200);
    $response->assertSee('Data Pelanggan');
});

test('customers list can be fetched via ajax', function () {
    $customer = User::factory()->create([
        'name' => 'Ahmad Pelanggan',
        'phone' => '081122334455',
        'tier' => 'VIP',
        'is_active' => '1',
    ]);
    $customer->assignRole('customer');

    $response = $this
        ->actingAs($this->user)
        ->getJson('/customers', [
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

test('customer can be created', function () {
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-001',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $payload = [
        'name' => 'Budi Baru',
        'phone' => '085544332211',
        'email' => 'budi@test.com',
        'dob' => '1995-10-15',
        'gender' => 'Laki-laki',
        'outlet_id' => $outlet->id,
        'tier' => 'Premium',
        'address' => 'Jl. Pahlawan No. 7',
        'notes' => 'Catatan budi',
        'is_active' => '1',
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson('/customers', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    
    $this->assertDatabaseHas('users', [
        'name' => 'Budi Baru',
        'phone' => '085544332211',
        'email' => 'budi@test.com',
        'gender' => 'male', // checks mapped value
        'tier' => 'Premium',
    ]);
});

test('customer details can be retrieved', function () {
    $customer = User::factory()->create([
        'name' => 'Siti Detail',
        'phone' => '082233445566',
        'tier' => 'Baru',
    ]);
    $customer->assignRole('customer');

    $response = $this
        ->actingAs($this->user)
        ->getJson("/customers/{$customer->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.name', 'Siti Detail');
});

test('customer can be updated', function () {
    $customer = User::factory()->create([
        'name' => 'Dedi Update',
        'phone' => '083344556677',
        'tier' => 'Reguler',
    ]);
    $customer->assignRole('customer');

    $payload = [
        'name' => 'Dedi Update Sukses',
        'phone' => '083344556677',
        'email' => 'dedi@test.com',
        'gender' => 'Perempuan',
        'tier' => 'VIP',
    ];

    $response = $this
        ->actingAs($this->user)
        ->putJson("/customers/{$customer->id}", $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('users', [
        'id' => $customer->id,
        'name' => 'Dedi Update Sukses',
        'gender' => 'female', // checks mapped value
        'tier' => 'VIP',
    ]);
});

test('customer can be deleted', function () {
    $customer = User::factory()->create([
        'name' => 'Riko Hapus',
        'phone' => '084455667788',
    ]);
    $customer->assignRole('customer');

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/customers/{$customer->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseMissing('users', [
        'id' => $customer->id,
    ]);
});
