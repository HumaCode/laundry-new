<?php

namespace App\Repositories\Contracts;

interface CustomerRepositoryInterface
{
    /**
     * Get paginated customers with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage);

    /**
     * Find customer by ID.
     */
    public function findById(string $id);

    /**
     * Create a new customer.
     */
    public function create(array $data);

    /**
     * Update an existing customer.
     */
    public function update(string $id, array $data);

    /**
     * Delete a customer.
     */
    public function delete(string $id);

    /**
     * Get summary statistics of customers.
     */
    public function getSummaryStats();
}
