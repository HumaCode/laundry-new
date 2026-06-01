<?php

use App\Models\User;
use App\Models\Operasional\Service;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('services page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/services');

    $response->assertStatus(200);
    $response->assertSee('Layanan & Harga', false);
});

test('services list can be fetched via ajax and contains pagination metadata', function () {
    Service::create([
        'service_code' => 'SVC-001',
        'name' => 'Cuci Kiloan Premium',
        'category' => 'kiloan',
        'price' => 12000,
        'unit' => '/kg',
        'status' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson('/services', [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
            'stats',
        ]
    ]);
});

test('service can be created', function () {
    $payload = [
        'name' => 'Setrika Kilat',
        'emoji' => '⚡',
        'category' => 'satuan',
        'description' => 'Setrika pakaian satuan dengan cepat',
        'price' => 5000,
        'unit' => '/pcs',
        'eta' => '3 jam',
        'color' => 'sc-orange',
        'status' => true,
        'express' => true,
        'pickup' => false,
        'target' => 300,
        'min_qty' => '1',
        'features' => ['Rapi', 'Wangi'],
        'tiers' => [
            ['label' => '1-5 pcs', 'price' => 5000],
        ]
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson('/services', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('services', [
        'name' => 'Setrika Kilat',
        'category' => 'satuan',
        'price' => 5000,
    ]);
});

test('service details can be retrieved', function () {
    $service = Service::create([
        'service_code' => 'SVC-002',
        'name' => 'Dry Clean Jas',
        'category' => 'satuan',
        'price' => 35000,
        'unit' => '/pcs',
        'status' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson("/services/{$service->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.name', 'Dry Clean Jas');
});

test('service can be updated', function () {
    $service = Service::create([
        'service_code' => 'SVC-003',
        'name' => 'Cuci Bedcover',
        'category' => 'satuan',
        'price' => 25000,
        'unit' => '/pcs',
        'status' => true,
    ]);

    $payload = [
        'name' => 'Cuci Bedcover Jumbo',
        'category' => 'satuan',
        'price' => 30000,
        'unit' => '/pcs',
        'status' => true,
    ];

    $response = $this
        ->actingAs($this->user)
        ->putJson("/services/{$service->id}", $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Cuci Bedcover Jumbo',
        'price' => 30000,
    ]);
});

test('service can be deleted', function () {
    $service = Service::create([
        'service_code' => 'SVC-004',
        'name' => 'Layanan Sementara',
        'category' => 'kiloan',
        'price' => 10000,
        'unit' => '/kg',
        'status' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/services/{$service->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertSoftDeleted('services', [
        'id' => $service->id,
    ]);
});

test('service status can be toggled', function () {
    $service = Service::create([
        'service_code' => 'SVC-005',
        'name' => 'Layanan Toggle',
        'category' => 'kiloan',
        'price' => 8000,
        'unit' => '/kg',
        'status' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->patchJson("/services/{$service->id}/toggle-status");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.status', false);

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'status' => false,
    ]);
});

test('services price can be adjusted in bulk', function () {
    $service1 = Service::create([
        'service_code' => 'SVC-B1',
        'name' => 'Cuci Kiloan Standard',
        'category' => 'kiloan',
        'price' => 10000,
        'unit' => '/kg',
        'status' => true,
    ]);

    $service2 = Service::create([
        'service_code' => 'SVC-B2',
        'name' => 'Cuci Kiloan Kilat',
        'category' => 'kiloan',
        'price' => 15000,
        'unit' => '/kg',
        'status' => true,
    ]);

    // Test increase by 10%
    $response = $this
        ->actingAs($this->user)
        ->postJson('/services/bulk-price', [
            'category' => 'kiloan',
            'type' => 'up',
            'adjustment_type' => 'percentage',
            'value' => 10,
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('services', [
        'id' => $service1->id,
        'price' => 11000,
    ]);

    $this->assertDatabaseHas('services', [
        'id' => $service2->id,
        'price' => 16500,
    ]);

    // Test decrease by flat Rp 2000
    $response = $this
        ->actingAs($this->user)
        ->postJson('/services/bulk-price', [
            'category' => 'all',
            'type' => 'down',
            'adjustment_type' => 'nominal',
            'value' => 2000,
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('services', [
        'id' => $service1->id,
        'price' => 9000,
    ]);
});
