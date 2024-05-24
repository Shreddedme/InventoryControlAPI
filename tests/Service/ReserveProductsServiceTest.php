<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Response\ProductReserveResponse;
use App\Service\ReserveProductsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReserveProductsServiceTest extends TestCase
{
    private ReserveProductsService $service;
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->service = new ReserveProductsService($this->entityManager, $this->productRepository);
    }

    public function testEmptyUniqueCodesShouldReturnNoReservedProducts()
    {
        $uniqueCodes = [];
        $expectedResponse = new ProductReserveResponse([], [], []);

        $actualResponse = $this->service->executeTransaction($uniqueCodes);

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testExceptionDuringTransactionShouldHandleRollback()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $uniqueCodes = ['LKB-RE-2024-00001'];
        $this->productRepository->expects($this->once())->method('findBy')->willThrowException(new \Exception('Database error'));

        $this->service->executeTransaction($uniqueCodes);
    }

    public function testReservedProductsAndOutOfStock()
    {
        $uniqueCodes = ['LKB-RE-2024-00001', 'LKB-RE-2024-0000', 'J-RE-2024-00002'];
        $product1 = (new Product())->setUniqueCode('LKB-RE-2024-00001')->setQuantity(5);
        $product2 = (new Product())->setUniqueCode('LKB-RE-2024-0000')->setQuantity(0);

        $this->productRepository->expects($this->once())
            ->method('findBy')
            ->with(['uniqueCode' => $uniqueCodes])
            ->willReturn([$product1, $product2]);

        $expectedResponse = new ProductReserveResponse(
            ['LKB-RE-2024-00001'],
            ['J-RE-2024-00002'],
            ['LKB-RE-2024-0000']
        );

        $actualResponse = $this->service->executeTransaction($uniqueCodes);

        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }
}