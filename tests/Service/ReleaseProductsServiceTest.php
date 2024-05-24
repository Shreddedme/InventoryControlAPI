<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Response\ProductReleaseResponse;
use App\Service\ReleaseProductsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReleaseProductsServiceTest extends TestCase
{
    private ReleaseProductsService $service;
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->service = new ReleaseProductsService($this->entityManager, $this->productRepository);
    }

    public function testEmptyUniqueCodesShouldReturnNoReleasedProducts()
    {
        $uniqueCodes = [];
        $expectedResponse = new ProductReleaseResponse([], []);

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

    public function testReleasedProducts()
    {
        $uniqueCodes = ['LKB-RE-2024-00001'];
        $product = (new Product())
            ->setUniqueCode('LKB-RE-2024-00001')
            ->setQuantity(5);

        $this->productRepository->expects($this->once())
            ->method('findBy')
            ->with(['uniqueCode' => $uniqueCodes])
            ->willReturn([$product]);

        $expectedResponse = new ProductReleaseResponse(
            ['LKB-RE-2024-00001'],
            []
        );

        $actualResponse = $this->service->executeTransaction($uniqueCodes);

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testProductNotReleasedWhenNotFound()
    {
        $uniqueCodes = ['LKB-RE-2024-00001', 'LKB-RE-2024-0000'];
        $product = (new Product())
            ->setUniqueCode('LKB-RE-2024-00001')
            ->setQuantity(5);

        $this->productRepository->expects($this->once())
            ->method('findBy')
            ->with(['uniqueCode' => $uniqueCodes])
            ->willReturn([$product]);

        $expectedResponse = new ProductReleaseResponse(
            ['LKB-RE-2024-00001'],
            ['LKB-RE-2024-0000']
        );

        $actualResponse = $this->service->executeTransaction($uniqueCodes);

        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }
}