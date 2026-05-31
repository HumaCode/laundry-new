<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

test('roles and permissions are created with ulids', function () {
    $role = Role::create(['name' => 'admin', 'slug' => 'admin', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

    expect($role->id)->toBeString()->toHaveLength(26)
        ->and($permission->id)->toBeString()->toHaveLength(26);
});

test('roles and permissions can be assigned to users', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'manager', 'slug' => 'manager', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'delete orders', 'guard_name' => 'web']);

    $user->assignRole($role);
    $user->givePermissionTo($permission);

    expect($user->hasRole('manager'))->toBeTrue()
        ->and($user->hasPermissionTo('delete orders'))->toBeTrue();
});
