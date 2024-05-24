<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Response\ProductReleaseResponse;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ReleaseProductsService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ProductRepository $productRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function executeTransaction(array $uniqueCodes): ProductReleaseResponse
    {
        $this->entityManager->beginTransaction();
        try {
            $products = empty($uniqueCodes) ? [] : $this->productRepository->findBy(['uniqueCode' => $uniqueCodes]);
            $response = $this->processProducts($products, $uniqueCodes);

            $this->entityManager->flush();
            $this->entityManager->commit();

            return new ProductReleaseResponse(
                $response['released_products'] ?? [],
                $response['not_found'] ?? []
            );
        } catch (Exception $e) {
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
        $releasedProducts = [];

        foreach ($products as $product) {
            $product->release();
            $releasedProducts[] = $product->getUniqueCode();
        }

        $response['released_products'] = !empty($releasedProducts) ? $releasedProducts : null;

        $notFoundCodes = array_values(array_diff($uniqueCodes, $releasedProducts));
        $response['not_found'] = !empty($notFoundCodes) ? $notFoundCodes : null;

        return $response;
    }
}
