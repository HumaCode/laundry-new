<?php

namespace App\Services;

use App\Repositories\Contracts\BusinessRepositoryInterface;

class BusinessService
{
    protected $businessRepository;

    public function __construct(BusinessRepositoryInterface $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * Get list of businesses (paginated).
     */
    public function getPaginatedBusinesses(array $filters, int $perPage = 10)
    {
        return $this->businessRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get business detail.
     */
    public function getBusinessById(string $id)
    {
        return $this->businessRepository->findById($id);
    }

    /**
     * Create business.
     */
    public function createBusiness(array $data)
    {
        if (empty($data['code'])) {
            $data['code'] = 'BIS-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        return $this->businessRepository->create($data);
    }

    /**
     * Update business.
     */
    public function updateBusiness(string $id, array $data)
    {
        return $this->businessRepository->update($id, $data);
    }

    /**
     * Delete business.
     */
    public function deleteBusiness(string $id)
    {
        return $this->businessRepository->delete($id);
    }

    /**
     * Get summary statistics.
     */
    public function getSummaryStats()
    {
        return $this->businessRepository->getSummaryStats();
    }

    /**
     * Get all businesses (for dropdowns).
     */
    public function getAllBusinesses()
    {
        return $this->businessRepository->getAll();
    }
}
