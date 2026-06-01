<?php

namespace App\Repositories\Contracts;

interface PickupRepositoryInterface
{
    public function getPaginated(array $filters, int $perPage);
    public function getSummaryStats();
    public function findById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}
