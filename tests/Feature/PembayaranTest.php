<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Master\Outlet;
use App\Models\Master\Order;

beforeEach(function () {
    // Create necessary roles
    Role::updateOrCreate(
        ['slug' => 'customer'],
        ['name' => 'customer', 'slug' => 'customer', 'guard_name' => 'web']
    );

    $this->user = User::factory()->create();
});

test('payments page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/payments');

    $response->assertStatus(200);
    $response->assertSee('Data Pembayaran');
});

test('payments list can be fetched via ajax/json request', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-PAY01',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    Order::create([
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Kering',
        'weight' => 4.0,
        'price_per_unit' => 7000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson('/payments', [
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

test('payment details can be retrieved', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-PAY02',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Kering',
        'weight' => 2.0,
        'price_per_unit' => 7000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson("/payments/{$order->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.service_type', 'Cuci Kering');
    $response->assertJsonPath('data.payment_status', 'Belum');
});

test('payment status and method can be updated', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-PAY03',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Kering',
        'weight' => 3.0,
        'price_per_unit' => 7000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->putJson("/payments/{$order->id}", [
            'payment_status' => 'Lunas',
            'payment_method' => 'QRIS',
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.payment_status', 'Lunas');
    $response->assertJsonPath('data.payment_method', 'QRIS');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'payment_status' => 'Lunas',
        'payment_method' => 'QRIS',
    ]);
});
