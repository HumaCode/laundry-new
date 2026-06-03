<?php

namespace App\Repositories\Contracts;

use App\Models\Role;

interface RoleRepositoryInterface
{
    /**
     * Get all roles with their permissions.
     */
    public function getAllWithPermissions();

    /**
     * Get all active menus with permissions.
     */
    public function getAllMenus();

    /**
     * Get all active users with their outlet.
     */
    public function getAllActiveUsers();

    /**
     * Get role-users mapping.
     */
    public function getRoleUsersMap(iterable $roles);

    /**
     * Find a role by its ID.
     */
    public function findById(string $id);

    /**
     * Create a new role.
     */
    public function create(array $data);

    /**
     * Update an existing role.
     */
    public function update(string $id, array $data);

    /**
     * Delete a role.
     */
    public function delete(string $id);

    /**
     * Sync permissions to a role.
     */
    public function syncPermissions(Role $role, array $permissions);

    /**
     * Assign a user to a role.
     */
    public function assignUser(Role $role, string $userId);

    /**
     * Remove a user from a role.
     */
    public function removeUser(Role $role, string $userId);
}
