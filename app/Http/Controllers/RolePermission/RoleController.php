<?php

namespace App\Http\Controllers\RolePermission;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermission\StoreRoleRequest;
use App\Http\Requests\RolePermission\UpdateRoleRequest;
use App\Http\Requests\RolePermission\SyncPermissionsRequest;
use App\Http\Requests\RolePermission\AssignUsersRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    protected $roleService;

    /**
     * RoleController constructor.
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data = $this->roleService->getIndexData();

        return view('pages.role_permission.role.index', $data);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->roleService->createRole($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan',
                'data' => new RoleResource($role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, $id): JsonResponse
    {
        try {
            $role = $this->roleService->updateRole($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diperbarui',
                'data' => new RoleResource($role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->roleService->deleteRole($id);

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update permissions for a specific role.
     */
    public function updatePermissions(SyncPermissionsRequest $request, $id): JsonResponse
    {
        try {
            $role = $this->roleService->syncPermissions($id, $request->input('permissions', []));

            return response()->json([
                'success' => true,
                'message' => 'Permission untuk role ' . $role->name . ' berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign multiple users to a role.
     */
    public function assignUsers(AssignUsersRequest $request, $id): JsonResponse
    {
        try {
            $userIds = $request->input('user_ids');
            $role = $this->roleService->assignUsers($id, $userIds);

            return response()->json([
                'success' => true,
                'message' => count($userIds) . ' pengguna berhasil ditambahkan ke role ' . $role->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pengguna: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a user from a role.
     */
    public function removeUser($id, $userId): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            $user = \App\Models\User::findOrFail($userId);
            $this->roleService->removeUser($id, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Pengguna ' . $user->name . ' berhasil dihapus dari role ' . $role->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengguna dari role: ' . $e->getMessage()
            ], 500);
        }
    }
}
