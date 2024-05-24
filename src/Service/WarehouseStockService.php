<?php

namespace App\Service;

use App\Repository\WarehouseRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class WarehouseStockService
{
    public function __construct(
        private WarehouseRepository $warehouseRepository
    )
    {
    }

    public function getWarehouseStock(int $warehouseId): array
    {
        $warehouse = $this->warehouseRepository->find($warehouseId);

        if ($warehouse) {
            $products = $warehouse->getProducts()->toArray();
        } else {
            throw new \Exception('Warehouse not found');
        }

        return [
            'message' => 'Stock count',
            'stock' => array_map(function ($product) {
                return [
                    'uniqueCode' => $product->getUniqueCode(),
                    'quantity' => $product->getQuantity()
                ];
            }, $products)
        ];
    }
}