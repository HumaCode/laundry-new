<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get data for role management index page.
     */
    public function getIndexData()
    {
        $roles = $this->roleRepository->getAllWithPermissions();
        $menus = $this->roleRepository->getAllMenus();
        $users = $this->roleRepository->getAllActiveUsers();
        $roleUsers = $this->roleRepository->getRoleUsersMap($roles);

        return compact('roles', 'menus', 'users', 'roleUsers');
    }

    /**
     * Create a new role.
     */
    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $slug = $data['slug'] ?? Str::slug($data['name']);

            return $this->roleRepository->create([
                'name' => $data['name'],
                'slug' => $slug,
                'type_role' => $data['type_role'] ?? $slug,
                'color' => $data['color'] ?? '#6366F1',
                'icon' => $data['icon'] ?? 'fa-user-shield',
                'priority' => $data['priority'] ?? 2,
                'description' => $data['description'] ?? '',
                'guard_name' => 'web',
            ]);
        });
    }

    /**
     * Update an existing role.
     */
    public function updateRole(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $role = $this->roleRepository->findById($id);
            $slug = $data['slug'] ?? Str::slug($data['name']);

            return $this->roleRepository->update($id, [
                'name' => $data['name'],
                'slug' => $slug,
                'type_role' => $data['type_role'] ?? $slug,
                'color' => $data['color'] ?? $role->color,
                'icon' => $data['icon'] ?? $role->icon,
                'priority' => $data['priority'] ?? $role->priority,
                'description' => $data['description'] ?? $role->description,
            ]);
        });
    }

    /**
     * Delete a role.
     */
    public function deleteRole(string $id)
    {
        return DB::transaction(function () use ($id) {
            $role = $this->roleRepository->findById($id);

            if ($role->slug === 'dev' || $role->slug === 'admin') {
                throw new \InvalidArgumentException('Role bawaan sistem tidak boleh dihapus.');
            }

            if ($role->users()->count() > 0) {
                throw new \InvalidArgumentException('Role masih memiliki pengguna aktif. Kosongkan pengguna terlebih dahulu.');
            }

            return $this->roleRepository->delete($id);
        });
    }

    /**
     * Sync permissions to a role.
     */
    public function syncPermissions(string $id, array $permissions)
    {
        return DB::transaction(function () use ($id, $permissions) {
            $role = $this->roleRepository->findById($id);
            $this->roleRepository->syncPermissions($role, $permissions);
            return $role;
        });
    }

    /**
     * Assign multiple users to a role.
     */
    public function assignUsers(string $id, array $userIds)
    {
        return DB::transaction(function () use ($id, $userIds) {
            $role = $this->roleRepository->findById($id);
            foreach ($userIds as $userId) {
                $this->roleRepository->assignUser($role, $userId);
            }
            return $role;
        });
    }

    /**
     * Remove a user from a role.
     */
    public function removeUser(string $id, string $userId)
    {
        return DB::transaction(function () use ($id, $userId) {
            $role = $this->roleRepository->findById($id);
            $this->roleRepository->removeUser($role, $userId);
            return $role;
        });
    }
}
