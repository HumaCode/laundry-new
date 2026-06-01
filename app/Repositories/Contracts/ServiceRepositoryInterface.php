<?php

namespace App\Repositories\Contracts;

interface ServiceRepositoryInterface
{
    public function getAll(array $filters, ?int $perPage = null);
    public function getSummaryStats();
    public function findById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function toggleStatus(string $id);
    public function bulkPriceUpdate(string $category, string $type, string $adjustmentType, float $value);
}
