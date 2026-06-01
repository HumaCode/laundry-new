<?php

namespace App\Services;

use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get list of customers.
     */
    public function getPaginatedCustomers(array $filters, int $perPage = 10)
    {
        return $this->customerRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get customer details.
     */
    public function getCustomerById(string $id)
    {
        return $this->customerRepository->findById($id);
    }

    /**
     * Create customer.
     */
    public function createCustomer(array $data)
    {
        return $this->customerRepository->create($data);
    }

    /**
     * Update customer.
     */
    public function updateCustomer(string $id, array $data)
    {
        return $this->customerRepository->update($id, $data);
    }

    /**
     * Delete customer.
     */
    public function deleteCustomer(string $id)
    {
        return $this->customerRepository->delete($id);
    }

    /**
     * Get summary statistics of customers.
     */
    public function getSummaryStats()
    {
        return $this->customerRepository->getSummaryStats();
    }
}
