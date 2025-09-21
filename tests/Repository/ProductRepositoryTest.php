<?php

namespace App\Tests\Repository;

use App\Entity\ProductEntity;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use App\Repository\ProductEntityRepository;
use App\Repository\ProductRepository;
use App\Service\Mapper\ProductMapper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    public function testFindAll(): void
    {
        $productEntity1 = $this->createStub(ProductEntity::class);
        $productEntity2 = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(ProductEntityRepository::class);
        $entityRepository
            ->method('findAll')
            ->willReturn([$productEntity1, $productEntity2]);

        $expectedProduct1 = $this->createStub(Product::class);
        $expectedProduct2 = $this->createStub(Product::class);
        $mapper = $this->createStub(ProductMapper::class);
        $mapper
            ->method('entityToModel')
            ->willReturnMap([
                [$productEntity1, $expectedProduct1],
                [$productEntity2, $expectedProduct2],
            ]);

        $repository = new ProductRepository($entityRepository, $mapper);
        $results = $repository->findAll();

        self::assertSame([$expectedProduct1, $expectedProduct2], $results);
    }

    public function testFind(): void
    {
        $id = 123;

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(ProductEntityRepository::class);
        $entityRepository
            ->method('find')
            ->with($id)
            ->willReturn($productEntity);

        $expectedProduct = $this->createStub(Product::class);
        $mapper = $this->createStub(ProductMapper::class);
        $mapper
            ->method('entityToModel')
            ->with($productEntity)
            ->willReturn($expectedProduct);

        $repository = new ProductRepository($entityRepository, $mapper);
        $foundProduct = $repository->find($id);

        self::assertSame($expectedProduct, $foundProduct);
    }

    public function testFindWhenNotFound(): void
    {
        $id = 123;

        $entityRepository = $this->createStub(ProductEntityRepository::class);
        $entityRepository
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityRepository, $mapper);
        $foundProduct = $repository->find($id);

        self::assertNull($foundProduct);
    }

    public function testCreate(): void
    {
        $product = $this->createStub(Product::class);

        $productEntity = $this->createStub(ProductEntity::class);

        $entityRepository = $this->createMock(ProductEntityRepository::class);
        $entityRepository
            ->expects($this->once())
            ->method('save')
            ->with($productEntity);

        $mapper = $this->createStub(ProductMapper::class);

        $mapper
            ->method('modelToEntity')
            ->with($product)
            ->willReturn($productEntity);

        $finalProduct = $this->createStub(Product::class);
        $mapper
            ->method('entityToModel')
            ->with($productEntity)
            ->willReturn($finalProduct);

        $repository = new ProductRepository($entityRepository, $mapper);
        $createdProduct = $repository->create($product);

        self::assertSame($finalProduct, $createdProduct);
    }

    public function testUpdate(): void
    {
        $product = new Product(
            123,
            'Test Product',
            new Price(1000),
            new NoDiscountStrategy()
        );

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createMock(ProductEntityRepository::class);
        $entityRepository
            ->method('find')
            ->with(123)
            ->willReturn($productEntity);
        $entityRepository
            ->expects($this->once())
            ->method('save')
            ->with($productEntity);

        $mapper = $this->createStub(ProductMapper::class);
        $mapper
            ->method('modelToEntity')
            ->with($product)
            ->willReturn($productEntity);

        $finalProduct = $this->createStub(Product::class);
        $mapper
            ->method('entityToModel')
            ->with($productEntity)
            ->willReturn($finalProduct);

        $repository = new ProductRepository($entityRepository, $mapper);
        $updatedProduct = $repository->update($product);

        self::assertSame($finalProduct, $updatedProduct);
    }

    public function testUpdateWhenNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product does not exist');

        $product = new Product(
            123,
            'Test Product',
            new Price(1000),
            new NoDiscountStrategy()
        );

        $entityRepository = $this->createMock(ProductEntityRepository::class);
        $entityRepository
            ->method('find')
            ->with(123)
            ->willReturn(null);
        $entityRepository
            ->expects($this->never())
            ->method('save');

        $mapper = $this->createStub(ProductMapper::class);
        $mapper
            ->method('modelToEntity')
            ->willReturn($this->createStub(ProductEntity::class));

        $mapper
            ->method('entityToModel')
            ->willReturn($this->createStub(Product::class));

        $repository = new ProductRepository($entityRepository, $mapper);
        $repository->update($product);
    }

    public function testDelete(): void
    {
        $id = 123;

        $entityRepository = $this->createMock(ProductEntityRepository::class);
        $entityRepository
            ->method('exists')
            ->with($id)
            ->willReturn(true);
        $entityRepository
            ->expects($this->once())
            ->method('delete')
            ->with($id);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityRepository, $mapper);
        $repository->delete($id);
    }

    public function testDeleteWhenNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product does not exist');

        $id = 123;

        $entityRepository = $this->createMock(ProductEntityRepository::class);
        $entityRepository
            ->method('exists')
            ->with($id)
            ->willReturn(false);
        $entityRepository
            ->expects($this->never())
            ->method('delete')
            ->with($id);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityRepository, $mapper);
        $repository->delete($id);
    }

    public function testExistsWhenFound(): void
    {
        $id = 123;

        $entityRepository = $this->createStub(ProductEntityRepository::class);
        $entityRepository
            ->method('exists')
            ->with($id)
            ->willReturn(true);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityRepository, $mapper);
        $result = $repository->exists($id);

        self::assertTrue($result);
    }

    public function testExistsWhenNotFound(): void
    {
        $id = 123;

        $entityRepository = $this->createStub(ProductEntityRepository::class);
        $entityRepository
            ->method('exists')
            ->with($id)
            ->willReturn(false);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityRepository, $mapper);
        $result = $repository->exists($id);

        self::assertFalse($result);
    }
}
