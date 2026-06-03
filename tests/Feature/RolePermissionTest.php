<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

beforeEach(function () {
    // Seed default roles and permissions
    $this->devRole = Role::updateOrCreate(
        ['slug' => 'dev'],
        ['name' => 'dev', 'slug' => 'dev', 'guard_name' => 'web', 'priority' => 1]
    );

    $this->adminRole = Role::updateOrCreate(
        ['slug' => 'admin'],
        ['name' => 'admin', 'slug' => 'admin', 'guard_name' => 'web', 'priority' => 2]
    );

    $this->kasirRole = Role::updateOrCreate(
        ['slug' => 'kasir'],
        ['name' => 'kasir', 'slug' => 'kasir', 'guard_name' => 'web', 'priority' => 3]
    );

    // Create test user
    $this->user = User::factory()->create();
    $this->user->assignRole($this->adminRole);
});

test('roles page is displayed to authenticated user', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/roles');

    $response->assertStatus(200);
    $response->assertSee('Daftar Role');
    $response->assertSee('Pilih Role');
});

test('role can be created', function () {
    $payload = [
        'name' => 'Supervisor Laundry',
        'slug' => 'spv',
        'description' => 'Supervisor of outlet operations',
        'color' => '#8B5CF6',
        'icon' => 'fa-user-tie',
        'priority' => 3,
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson('/roles', $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.name', 'Supervisor Laundry');

    $this->assertDatabaseHas('roles', [
        'name' => 'Supervisor Laundry',
        'slug' => 'spv',
        'color' => '#8B5CF6',
        'icon' => 'fa-user-tie',
    ]);
});

test('role can be updated', function () {
    $role = Role::create([
        'name' => 'Manager Toko',
        'slug' => 'manager-toko',
        'guard_name' => 'web',
        'color' => '#F97316',
        'icon' => 'fa-user-cog',
        'priority' => 3
    ]);

    $payload = [
        'name' => 'Manager Toko Edit',
        'slug' => 'manager-toko-edit',
        'description' => 'Updated desc',
        'color' => '#EC4899',
        'icon' => 'fa-user-tie',
        'priority' => 2,
    ];

    $response = $this
        ->actingAs($this->user)
        ->putJson("/roles/{$role->id}", $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => 'Manager Toko Edit',
        'slug' => 'manager-toko-edit',
        'color' => '#EC4899',
    ]);
});

test('role can be deleted if no users assigned', function () {
    $role = Role::create([
        'name' => 'Temporary Role',
        'slug' => 'temp',
        'guard_name' => 'web'
    ]);

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/roles/{$role->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseMissing('roles', [
        'id' => $role->id,
    ]);
});

test('system roles cannot be deleted', function () {
    // Attempting to delete dev role
    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/roles/{$this->devRole->id}");

    $response->assertStatus(422);
    $response->assertJsonPath('success', false);
    $response->assertJsonPath('message', 'Role bawaan sistem tidak boleh dihapus.');

    $this->assertDatabaseHas('roles', [
        'id' => $this->devRole->id,
    ]);
});

test('role cannot be deleted if users are assigned', function () {
    $role = Role::create([
        'name' => 'Busy Role',
        'slug' => 'busy',
        'guard_name' => 'web'
    ]);

    $busyUser = User::factory()->create();
    $busyUser->assignRole($role);

    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/roles/{$role->id}");

    $response->assertStatus(422);
    $response->assertJsonPath('success', false);

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
    ]);
});

test('role permissions can be updated', function () {
    $permission1 = Permission::create(['name' => 'menu services', 'guard_name' => 'web']);
    $permission2 = Permission::create(['name' => 'create services', 'guard_name' => 'web']);

    $role = Role::create([
        'name' => 'Staff Biasa',
        'slug' => 'staff-biasa',
        'guard_name' => 'web'
    ]);

    $payload = [
        'permissions' => ['menu services', 'create services']
    ];

    $response = $this
        ->actingAs($this->user)
        ->postJson("/roles/{$role->id}/permissions", $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    expect($role->hasPermissionTo('menu services'))->toBeTrue()
        ->and($role->hasPermissionTo('create services'))->toBeTrue();
});

test('users can be assigned and removed from a role', function () {
    $role = Role::create([
        'name' => 'Staff Outlet',
        'slug' => 'staff-outlet',
        'guard_name' => 'web'
    ]);

    $employee = User::factory()->create();

    // Assign
    $response = $this
        ->actingAs($this->user)
        ->postJson("/roles/{$role->id}/users", [
            'user_ids' => [$employee->id]
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    expect($employee->hasRole($role->name))->toBeTrue();

    // Remove
    $response = $this
        ->actingAs($this->user)
        ->deleteJson("/roles/{$role->id}/users/{$employee->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    expect($employee->fresh()->hasRole($role->name))->toBeFalse();
});
