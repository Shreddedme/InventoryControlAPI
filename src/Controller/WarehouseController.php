<?php

namespace App\Controller;

use App\Service\WarehouseStockService;
use App\Service\ReleaseProductsService;
use App\Service\ReserveProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WarehouseController extends AbstractController
{
    public function __construct(
        private WarehouseStockService $productsService,
        private ReserveProductsService $reserveProductsService,
        private ReleaseProductsService $releaseProductsService
    )
    {
    }

    #[Route('/api/reserve-products', name: 'reserve-products', methods: ['POST'])]
    public function reserveProducts(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uniqueCodes = $data['uniqueCodes'] ?? [];

        return $this->json($this->reserveProductsService->executeTransaction($uniqueCodes));
    }

    #[Route('/api/release-products', name: 'release-products', methods: ['POST'])]
    public function releaseProducts(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uniqueCodes = $data['uniqueCodes'] ?? [];

        return $this->json($this->releaseProductsService->executeTransaction($uniqueCodes));
    }

    #[Route('/api/warehouse-stock/{id}', name: 'warehouse-stock', methods: ['GET'])]
    public function getWarehouseStock(int $id): JsonResponse
    {
        return $this->json($this->productsService->getWarehouseStock($id));
    }
}
