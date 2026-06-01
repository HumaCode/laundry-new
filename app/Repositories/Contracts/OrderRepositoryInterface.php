<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    /**
     * Get paginated orders with filtering and sorting.
     */
    public function getPaginated(array $filters, int $perPage);

    /**
     * Find order by ID.
     */
    public function findById(string $id);

    /**
     * Create a new order.
     */
    public function create(array $data);

    /**
     * Update an existing order.
     */
    public function update(string $id, array $data);

    /**
     * Delete an order.
     */
    public function delete(string $id);

    /**
     * Get summary statistics of orders.
     */
    public function getSummaryStats();
}
