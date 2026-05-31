<?php

namespace App\Services;

use App\Repositories\Contracts\OutletRepositoryInterface;

class OutletService
{
    protected $outletRepository;

    public function __construct(OutletRepositoryInterface $outletRepository)
    {
        $this->outletRepository = $outletRepository;
    }

    /**
     * Get list of outlets.
     */
    public function getPaginatedOutlets(array $filters, int $perPage = 10)
    {
        return $this->outletRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get outlet details.
     */
    public function getOutletById(string $id)
    {
        return $this->outletRepository->findById($id);
    }

    /**
     * Create outlet.
     */
    public function createOutlet(array $data)
    {
        if (empty($data['code'])) {
            $data['code'] = 'OUT-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        return $this->outletRepository->create($data);
    }

    /**
     * Update outlet.
     */
    public function updateOutlet(string $id, array $data)
    {
        return $this->outletRepository->update($id, $data);
    }

    /**
     * Delete outlet.
     */
    public function deleteOutlet(string $id)
    {
        return $this->outletRepository->delete($id);
    }

    /**
     * Get summary statistics of outlets.
     */
    public function getSummaryStats()
    {
        return $this->outletRepository->getSummaryStats();
    }

    /**
     * Get all outlets.
     */
    public function getAllOutlets()
    {
        return $this->outletRepository->getAll();
    }
}
