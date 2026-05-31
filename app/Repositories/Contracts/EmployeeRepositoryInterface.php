<?php

namespace App\Repositories\Contracts;

interface EmployeeRepositoryInterface
{
    /**
     * Get paginated employees with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage);

    /**
     * Find employee by ID.
     */
    public function findById(string $id);

    /**
     * Create a new employee.
     */
    public function create(array $data);

    /**
     * Update an existing employee.
     */
    public function update(string $id, array $data);

    /**
     * Delete an employee.
     */
    public function delete(string $id);

    /**
     * Get summary statistics of employees.
     */
    public function getSummaryStats();
}
