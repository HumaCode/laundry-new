<?php

namespace App\Repositories\Contracts;

interface InventoryRepositoryInterface
{
    public function getFiltered(array $filters);
    public function getSummaryStats();
    public function findById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}
