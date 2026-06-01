<?php

namespace App\Repositories\Contracts;

interface BusinessRepositoryInterface
{
    /**
     * Get paginated businesses with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage);

    /**
     * Find business by ID.
     */
    public function findById(string $id);

    /**
     * Create a new business.
     */
    public function create(array $data);

    /**
     * Update an existing business.
     */
    public function update(string $id, array $data);

    /**
     * Delete a business.
     */
    public function delete(string $id);

    /**
     * Get summary statistics of businesses.
     */
    public function getSummaryStats();

    /**
     * Get all businesses (for dropdowns).
     */
    public function getAll();
}
