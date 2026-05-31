<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeRepositoryInterface;

class EmployeeService
{
    protected $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Get list of employees.
     */
    public function getPaginatedEmployees(array $filters, int $perPage = 10)
    {
        return $this->employeeRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get employee details.
     */
    public function getEmployeeById(string $id)
    {
        return $this->employeeRepository->findById($id);
    }

    /**
     * Create employee.
     */
    public function createEmployee(array $data)
    {
        if (empty($data['code'])) {
            $data['code'] = 'EMP-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        return $this->employeeRepository->create($data);
    }

    /**
     * Update employee.
     */
    public function updateEmployee(string $id, array $data)
    {
        return $this->employeeRepository->update($id, $data);
    }

    /**
     * Delete employee.
     */
    public function deleteEmployee(string $id)
    {
        return $this->employeeRepository->delete($id);
    }

    /**
     * Get summary statistics of employees.
     */
    public function getSummaryStats()
    {
        return $this->employeeRepository->getSummaryStats();
    }
}
