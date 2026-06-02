<?php

namespace App\Services;

use App\Repositories\Contracts\InventoryRepositoryInterface;

class InventoryService
{
    protected $inventoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function getFilteredInventories(array $filters)
    {
        return $this->inventoryRepository->getFiltered($filters);
    }

    public function getSummaryStats()
    {
        return $this->inventoryRepository->getSummaryStats();
    }

    public function getInventoryById(string $id)
    {
        return $this->inventoryRepository->findById($id);
    }

    public function createInventory(array $data)
    {
        $data['history'] = [];
        return $this->inventoryRepository->create($data);
    }

    public function updateInventory(string $id, array $data)
    {
        return $this->inventoryRepository->update($id, $data);
    }

    public function deleteInventory(string $id)
    {
        return $this->inventoryRepository->delete($id);
    }

    public function restockInventory(string $id, array $restockData)
    {
        $item = $this->inventoryRepository->findById($id);

        $newStock = $item->stock + $restockData['qty'];

        // Update history array
        $history = $item->history ?? [];
        array_unshift($history, [
            'date' => $restockData['date'],
            'qty' => (int) $restockData['qty'],
            'supplier' => $restockData['supplier'] ?? '—',
            'invoice' => $restockData['invoice'] ?? '—',
        ]);

        // Keep last 15 history items to save space
        if (count($history) > 15) {
            $history = array_slice($history, 0, 15);
        }

        $updateData = [
            'stock' => $newStock,
            'last_restock' => $restockData['date'],
            'last_restock_qty' => $restockData['qty'],
            'history' => $history,
        ];

        if (!empty($restockData['price'])) {
            $updateData['price'] = $restockData['price'];
        }

        return $this->inventoryRepository->update($id, $updateData);
    }

    public function autoRestock()
    {
        $items = $this->inventoryRepository->getBelowMinStock();
        $count = 0;

        foreach ($items as $item) {
            $qty = $item->max_stock - $item->stock;
            if ($qty <= 0) {
                $qty = $item->min_stock > 0 ? $item->min_stock * 2 : 10;
            }

            $this->restockInventory($item->id, [
                'qty' => $qty,
                'date' => date('Y-m-d'),
                'supplier' => 'Sistem Otomatis',
                'invoice' => 'AUTO-' . date('YmdHis') . '-' . mt_rand(10, 99),
            ]);

            $count++;
        }

        return $count;
    }
}
