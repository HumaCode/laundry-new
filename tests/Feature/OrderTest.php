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

test('orders page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/orders');

    $response->assertStatus(200);
    $response->assertSee('Semua Order');
});

test('orders list can be fetched via ajax', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-001',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    Order::create([
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Setrika',
        'weight' => 2.5,
        'price_per_unit' => 8000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson('/orders', [
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

test('order can be created', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-001',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $payload = [
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Setrika',
        'weight' => 3,
        'price_per_unit' => 8000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
        'payment_method' => 'Tunai',
        'notes' => 'Tolong pisahkan pakaian putih',
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson('/orders', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    
    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Setrika',
        'weight' => 3,
        'total_price' => 24000,
    ]);
});

test('order details can be retrieved', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-001',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Setrika',
        'weight' => 2,
        'price_per_unit' => 8000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson("/orders/{$order->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.service_type', 'Cuci Setrika');
});

test('order can be updated', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-001',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Setrika',
        'weight' => 2,
        'price_per_unit' => 8000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
    ]);

    $payload = [
        'order_status' => 'Proses',
        'payment_status' => 'Lunas',
    ];

    $response = $this
        ->actingAs($this->user)
        ->putJson("/orders/{$order->id}", $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'order_status' => 'Proses',
        'payment_status' => 'Lunas',
    ]);
});

test('order can be deleted', function () {
    $customer = User::factory()->create();
    $customer->assignRole('customer');
    
    $outlet = Outlet::create([
        'name' => 'Outlet Test',
        'code' => 'OT-001',
        'phone' => '0812233445',
        'is_active' => true,
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'outlet_id' => $outlet->id,
        'service_type' => 'Cuci Setrika',
        'weight' => 2,
        'price_per_unit' => 8000,
        'order_status' => 'Baru',
        'payment_status' => 'Belum',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/orders/{$order->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertSoftDeleted('orders', [
        'id' => $order->id,
    ]);
});
