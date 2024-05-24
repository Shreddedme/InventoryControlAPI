<?php

namespace App\Service;

use App\Entity\Product;
use App\Exception\ProductOutOfStockException;
use App\Repository\ProductRepository;
use App\Response\ProductReserveResponse;
use Doctrine\ORM\EntityManagerInterface;

class ReserveProductsService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ProductRepository $productRepository
    ) {
    }

    /**
     * @throws \Exception
     */
    public function executeTransaction(array $uniqueCodes): ProductReserveResponse
    {
        $this->entityManager->beginTransaction();
        try {
            $products = empty($uniqueCodes) ? [] : $this->productRepository->findBy(['uniqueCode' => $uniqueCodes]);
            $response = $this->processProducts($products, $uniqueCodes);

            $this->entityManager->flush();
            $this->entityManager->commit();

            return new ProductReserveResponse(
                $response['reserved_products'] ?? [],
                $response['not_found'] ?? [],
                $response['out_of_stock'] ?? []
            );
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * @param Product[] $products
     * @param array $uniqueCodes
     * @return array
     */
    protected function processProducts(array $products, array $uniqueCodes): array
    {
        $reservedProducts = [];
        $outOfStock = [];

        foreach ($products as $product) {
            try {
                $product->reserve();
                $reservedProducts[] = $product->getUniqueCode();
            } catch (ProductOutOfStockException) {
                $outOfStock[] = $product->getUniqueCode();
            }
        }

        $response['reserved_products'] = !empty($reservedProducts) ? $reservedProducts : null;

        $notFoundCodes = array_values(array_diff($uniqueCodes, $reservedProducts, $outOfStock));
        $response['not_found'] = !empty($notFoundCodes) ? $notFoundCodes : null;

        $response['out_of_stock'] = !empty($outOfStock) ? $outOfStock : null;

        return $response;
    }
}
