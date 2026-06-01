<?php

namespace App\Services;

use App\Repositories\Contracts\ServiceRepositoryInterface;

class ServiceService
{
    protected $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getAllServices(array $filters, ?int $perPage = null)
    {
        return $this->serviceRepository->getAll($filters, $perPage);
    }

    public function getServiceById(string $id)
    {
        return $this->serviceRepository->findById($id);
    }

    public function createService(array $data)
    {
        return $this->serviceRepository->create($data);
    }

    public function updateService(string $id, array $data)
    {
        return $this->serviceRepository->update($id, $data);
    }

    public function deleteService(string $id)
    {
        return $this->serviceRepository->delete($id);
    }

    public function toggleServiceStatus(string $id)
    {
        return $this->serviceRepository->toggleStatus($id);
    }

    public function getSummaryStats()
    {
        return $this->serviceRepository->getSummaryStats();
    }

    public function bulkPriceUpdate(string $category, string $type, string $adjustmentType, float $value)
    {
        return $this->serviceRepository->bulkPriceUpdate($category, $type, $adjustmentType, $value);
    }
}
