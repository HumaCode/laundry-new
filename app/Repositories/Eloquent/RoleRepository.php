<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Models\User;
use App\Models\Konfigurasi\Menu;
use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Get all roles with their permissions.
     */
    public function getAllWithPermissions()
    {
        return Role::with('permissions')->orderBy('priority')->get();
    }

    /**
     * Get all active menus with permissions.
     */
    public function getAllMenus()
    {
        return Menu::active()->with('permissions')->orderBy('orders')->get();
    }

    /**
     * Get all active users with their outlet.
     */
    public function getAllActiveUsers()
    {
        return User::with('outlet')->where('is_active', '1')->orderBy('name')->get();
    }

    /**
     * Get role-users mapping.
     */
    public function getRoleUsersMap(iterable $roles)
    {
        $roleUsers = [];
        foreach ($roles as $role) {
            $roleUsers[$role->id] = $role->users()->pluck('model_id')->toArray();
        }
        return $roleUsers;
    }

    /**
     * Find a role by its ID.
     */
    public function findById(string $id)
    {
        return Role::findOrFail($id);
    }

    /**
     * Create a new role.
     */
    public function create(array $data)
    {
        return Role::create($data);
    }

    /**
     * Update an existing role.
     */
    public function update(string $id, array $data)
    {
        $role = $this->findById($id);
        $role->update($data);
        return $role;
    }

    /**
     * Delete a role.
     */
    public function delete(string $id)
    {
        $role = $this->findById($id);
        return $role->delete();
    }

    /**
     * Sync permissions to a role.
     */
    public function syncPermissions(Role $role, array $permissions)
    {
        return $role->syncPermissions($permissions);
    }

    /**
     * Assign a user to a role.
     */
    public function assignUser(Role $role, string $userId)
    {
        $user = User::findOrFail($userId);
        return $user->syncRoles($role->name);
    }

    /**
     * Remove a user from a role.
     */
    public function removeUser(Role $role, string $userId)
    {
        $user = User::findOrFail($userId);
        return $user->removeRole($role->name);
    }
}
