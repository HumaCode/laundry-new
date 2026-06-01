<?php

namespace App\Services;

use App\Repositories\Contracts\PickupRepositoryInterface;

class PickupService
{
    protected $pickupRepository;

    public function __construct(PickupRepositoryInterface $pickupRepository)
    {
        $this->pickupRepository = $pickupRepository;
    }

    public function getPaginatedPickups(array $filters, int $perPage = 6)
    {
        return $this->pickupRepository->getPaginated($filters, $perPage);
    }

    public function getPickupById(string $id)
    {
        return $this->pickupRepository->findById($id);
    }

    public function createPickup(array $data)
    {
        return $this->pickupRepository->create($data);
    }

    public function updatePickup(string $id, array $data)
    {
        return $this->pickupRepository->update($id, $data);
    }

    public function deletePickup(string $id)
    {
        return $this->pickupRepository->delete($id);
    }

    public function getSummaryStats()
    {
        return $this->pickupRepository->getSummaryStats();
    }
}
