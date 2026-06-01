<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get list of orders.
     */
    public function getPaginatedOrders(array $filters, int $perPage = 10)
    {
        return $this->orderRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get order details.
     */
    public function getOrderById(string $id)
    {
        return $this->orderRepository->findById($id);
    }

    /**
     * Create order.
     */
    public function createOrder(array $data)
    {
        return $this->orderRepository->create($data);
    }

    /**
     * Update order.
     */
    public function updateOrder(string $id, array $data)
    {
        return $this->orderRepository->update($id, $data);
    }

    /**
     * Delete order.
     */
    public function deleteOrder(string $id)
    {
        return $this->orderRepository->delete($id);
    }

    /**
     * Get summary statistics of orders.
     */
    public function getSummaryStats()
    {
        return $this->orderRepository->getSummaryStats();
    }
}
