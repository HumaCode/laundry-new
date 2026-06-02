<?php

use App\Models\User;
use App\Models\Master\Outlet;
use App\Models\Operasional\Inventory;

beforeEach(function () {
    // Disable CSRF/Request Forgery checks for all tests in this file
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);

    $this->user = User::factory()->create();

    // Create outlet
    $this->outlet = Outlet::create([
        'name' => 'Outlet Pusat Test',
        'code' => 'OPT-001',
        'phone' => '08122334455',
        'is_active' => true,
    ]);
});

test('inventories page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/inventories');

    $response->assertSuccessful();
    $response->assertSee('Inventaris');
});

test('inventories list can be fetched via ajax', function () {
    Inventory::create([
        'name' => 'Deterjen Rinso Test',
        'code' => 'DET-TST-001',
        'brand' => 'PT Unilever',
        'category' => 'Deterjen & Kimia',
        'emoji' => '🧴',
        'color' => '#6366F1',
        'stock' => 50,
        'min_stock' => 10,
        'max_stock' => 100,
        'unit' => 'kg',
        'price' => 30000,
        'outlet_id' => $this->outlet->id,
        'desc' => 'Deterjen uji coba.',
        'history' => []
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson('/inventories', [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'items',
            'stats' => [
                'total_items',
                'cukup',
                'rendah',
                'kritis',
                'total_value'
            ]
        ]
    ]);
});

test('inventory item can be created', function () {
    $payload = [
        'name' => 'Plastik Kiloan Test',
        'code' => 'PLS-TST-002',
        'brand' => 'CV Kemasan Indah',
        'category' => 'Plastik & Kemasan',
        'emoji' => '📦',
        'color' => '#3B82F6',
        'stock' => 30,
        'min_stock' => 5,
        'max_stock' => 50,
        'unit' => 'pack',
        'price' => 12000,
        'outlet_id' => $this->outlet->id,
        'desc' => 'Plastik pelindung laundry.'
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson('/inventories', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    
    $this->assertDatabaseHas('inventories', [
        'name' => 'Plastik Kiloan Test',
        'code' => 'PLS-TST-002',
    ]);
});

test('inventory item details can be retrieved', function () {
    $item = Inventory::create([
        'name' => 'Sabun Mandi Test',
        'code' => 'SBN-TST-003',
        'brand' => 'PT Kao',
        'category' => 'Deterjen & Kimia',
        'emoji' => '🧴',
        'color' => '#6366F1',
        'stock' => 20,
        'min_stock' => 5,
        'max_stock' => 50,
        'unit' => 'pcs',
        'price' => 5000,
        'outlet_id' => $this->outlet->id,
        'desc' => 'Sabun mandi cair.',
        'history' => []
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson("/inventories/{$item->id}");

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.name', 'Sabun Mandi Test');
});

test('inventory item can be updated', function () {
    $item = Inventory::create([
        'name' => 'Pewangi Downy Test',
        'code' => 'PWG-TST-004',
        'brand' => 'P&G',
        'category' => 'Pewangi & Softener',
        'emoji' => '🌸',
        'color' => '#EC4899',
        'stock' => 15,
        'min_stock' => 10,
        'max_stock' => 50,
        'unit' => 'liter',
        'price' => 25000,
        'outlet_id' => $this->outlet->id,
        'desc' => 'Pewangi pakaian.',
        'history' => []
    ]);

    $payload = [
        'name' => 'Pewangi Downy Test Update',
        'code' => 'PWG-TST-004',
        'brand' => 'P&G',
        'category' => 'Pewangi & Softener',
        'emoji' => '🌸',
        'color' => '#EC4899',
        'stock' => 25,
        'min_stock' => 8,
        'max_stock' => 60,
        'unit' => 'liter',
        'price' => 24000,
        'outlet_id' => $this->outlet->id,
        'desc' => 'Pewangi wangi tahan lama.',
    ];

    $response = $this
        ->actingAs($this->user)
        ->putJson("/inventories/{$item->id}", $payload);

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('inventories', [
        'id' => $item->id,
        'name' => 'Pewangi Downy Test Update',
        'stock' => 25,
    ]);
});

test('inventory item can be restocked', function () {
    $item = Inventory::create([
        'name' => 'Nota Laundry Test',
        'code' => 'NTA-TST-005',
        'brand' => 'Kiky',
        'category' => 'ATK & Administrasi',
        'emoji' => '📋',
        'color' => '#F97316',
        'stock' => 10,
        'min_stock' => 5,
        'max_stock' => 30,
        'unit' => 'pcs',
        'price' => 8000,
        'outlet_id' => $this->outlet->id,
        'desc' => 'Nota rangkap.',
        'history' => []
    ]);

    $payload = [
        'qty' => 15,
        'supplier' => 'CV Kiky Paper',
        'invoice' => 'INV-PAPER-01',
        'price' => 8500,
        'date' => '2026-06-02'
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson("/inventories/{$item->id}/restock", $payload);

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('inventories', [
        'id' => $item->id,
        'stock' => 25, // 10 + 15
        'price' => 8500
    ]);
});

test('inventory item can be deleted', function () {
    $item = Inventory::create([
        'name' => 'Isolasi Test',
        'code' => 'ISO-TST-006',
        'brand' => 'Nachi',
        'category' => 'ATK & Administrasi',
        'emoji' => '📋',
        'color' => '#F97316',
        'stock' => 8,
        'min_stock' => 2,
        'max_stock' => 20,
        'unit' => 'pcs',
        'price' => 10000,
        'outlet_id' => $this->outlet->id,
        'desc' => 'Isolasi bening.',
        'history' => []
    ]);

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/inventories/{$item->id}");

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);

    $this->assertSoftDeleted('inventories', [
        'id' => $item->id,
    ]);
});

test('inventories can be automatically restocked by outlet', function () {
    // 1. Without outlet parameter, it should fail
    $response = $this
        ->actingAs($this->user)
        ->postJson('/inventories/auto-restock');

    $response->assertStatus(400);
    $response->assertJsonPath('success', false);

    // Create another outlet
    $outlet2 = Outlet::create([
        'name' => 'Outlet Cabang Test',
        'code' => 'OCT-002',
        'phone' => '08122334466',
        'is_active' => true,
    ]);

    // Create an item below min stock for outlet 1 (needs restock)
    $item1 = Inventory::create([
        'name' => 'Deterjen Kritis',
        'code' => 'DET-KRI-01',
        'category' => 'Deterjen & Kimia',
        'stock' => 2,
        'min_stock' => 10,
        'max_stock' => 100,
        'price' => 30000,
        'outlet_id' => $this->outlet->id,
        'history' => []
    ]);

    // Create an item below min stock for outlet 2 (needs restock but shouldn't be touched when restocking outlet 1)
    $item2 = Inventory::create([
        'name' => 'Sabun Kritis Outlet 2',
        'code' => 'SBN-OCT-02',
        'category' => 'Deterjen & Kimia',
        'stock' => 1,
        'min_stock' => 10,
        'max_stock' => 50,
        'price' => 5000,
        'outlet_id' => $outlet2->id,
        'history' => []
    ]);

    // 2. Restock only outlet 1
    $response = $this
        ->actingAs($this->user)
        ->postJson('/inventories/auto-restock', [
            'outlet' => $this->outlet->id,
        ]);

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('message', '1 barang berhasil di-restock otomatis');

    // Assert item1 was restocked to max_stock (100)
    $this->assertDatabaseHas('inventories', [
        'id' => $item1->id,
        'stock' => 100,
    ]);

    // Assert item2 (outlet 2) stock remains unchanged (1)
    $this->assertDatabaseHas('inventories', [
        'id' => $item2->id,
        'stock' => 1,
    ]);
});
