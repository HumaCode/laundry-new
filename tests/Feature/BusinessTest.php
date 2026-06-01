<?php

use App\Models\Master\Business;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('businesses page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/businesses');

    $response->assertStatus(200);
    $response->assertSee('Total Bisnis');
});

test('businesses list can be fetched via ajax', function () {
    Business::create([
        'name' => 'Bisnis Laundry A',
        'code' => 'B-001',
        'owner' => 'John Doe',
        'phone' => '08123456789',
        'email' => 'john@business.com',
        'city' => 'Jakarta',
        'address' => 'Jl. Kebon Jeruk No. 12',
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson('/businesses', [
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

test('business can be created', function () {
    $payload = [
        'name' => 'Bisnis Laundry B',
        'owner' => 'Jane Doe',
        'phone' => '08987654321',
        'email' => 'jane@business.com',
        'city' => 'Bandung',
        'address' => 'Jl. Dago No. 45',
        'description' => 'Cabang laundry Bandung',
        'is_active' => 1,
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson('/businesses', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    
    $this->assertDatabaseHas('businesses', [
        'name' => 'Bisnis Laundry B',
        'owner' => 'Jane Doe',
        'city' => 'Bandung',
    ]);
});

test('business details can be retrieved', function () {
    $business = Business::create([
        'name' => 'Bisnis Laundry C',
        'code' => 'B-003',
        'owner' => 'Bob Smith',
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->getJson("/businesses/{$business->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.name', 'Bisnis Laundry C');
});

test('business can be updated', function () {
    $business = Business::create([
        'name' => 'Bisnis Laundry D',
        'code' => 'B-004',
        'owner' => 'Alice Cooper',
        'is_active' => true,
    ]);

    $payload = [
        'name' => 'Bisnis Laundry D Updated',
        'owner' => 'Alice Cooper',
        'phone' => '08777777777',
        'is_active' => 0,
    ];

    $response = $this
        ->actingAs($this->user)
        ->putJson("/businesses/{$business->id}", $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('businesses', [
        'id' => $business->id,
        'name' => 'Bisnis Laundry D Updated',
        'is_active' => false,
    ]);
});

test('business can be deleted', function () {
    $business = Business::create([
        'name' => 'Bisnis Laundry E',
        'code' => 'B-005',
        'owner' => 'David Miller',
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/businesses/{$business->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertSoftDeleted('businesses', [
        'id' => $business->id,
    ]);
});
