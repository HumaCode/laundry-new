<?php

namespace App\Repositories\Eloquent;

use App\Models\Master\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    /**
     * Get paginated employees with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage)
    {
        $query = Employee::with('outlet');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhereHas('outlet', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'Aktif') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'Tidak Aktif' || $filters['status'] === 'Tutup') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Find employee by ID.
     */
    public function findById(string $id)
    {
        return Employee::with('outlet')->findOrFail($id);
    }

    /**
     * Create a new employee.
     */
    public function create(array $data)
    {
        return Employee::create($data);
    }

    /**
     * Update an existing employee.
     */
    public function update(string $id, array $data)
    {
        $employee = $this->findById($id);
        $employee->update($data);
        return $employee;
    }

    /**
     * Delete an employee.
     */
    public function delete(string $id)
    {
        $employee = $this->findById($id);
        return $employee->delete();
    }

    /**
     * Get summary statistics of employees.
     */
    public function getSummaryStats()
    {
        $allEmployees = Employee::all();
        $totalEmployees = $allEmployees->count();
        $activeEmployees = $allEmployees->where('is_active', true)->count();
        $inactiveEmployees = $allEmployees->where('is_active', false)->count();

        $rolesCount = $allEmployees->pluck('role')
            ->filter()
            ->map(fn($r) => strtolower(trim($r)))
            ->unique()
            ->count();

        $roles = $allEmployees->pluck('role')
            ->filter()
            ->unique(function ($item) {
                return strtolower(trim($item));
            })
            ->values()
            ->toArray();

        $activePercentage = $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100) : 0;

        return [
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'inactive_employees' => $inactiveEmployees,
            'roles_count' => $rolesCount,
            'active_percentage' => $activePercentage,
            'roles' => $roles
        ];
    }
}
