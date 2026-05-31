<?php

namespace App\Repositories\Contracts;

interface OutletRepositoryInterface
{
    /**
     * Get paginated outlets with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage);

    /**
     * Find outlet by ID.
     */
    public function findById(string $id);

    /**
     * Create a new outlet.
     */
    public function create(array $data);

    /**
     * Update an existing outlet.
     */
    public function update(string $id, array $data);

    /**
     * Delete an outlet.
     */
    public function delete(string $id);
}
