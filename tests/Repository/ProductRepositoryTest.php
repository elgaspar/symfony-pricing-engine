<?php

namespace App\Tests\Repository;

use App\Entity\ProductEntity;
use App\Model\Product;
use App\Repository\ProductRepository;
use App\Service\Mapper\ProductMapper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    public function testFindAll(): void
    {
        $productEntity1 = $this->createStub(ProductEntity::class);
        $productEntity2 = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('findAll')
            ->willReturn([$productEntity1, $productEntity2]);

        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);

        $expectedProduct1 = $this->createStub(Product::class);
        $expectedProduct2 = $this->createStub(Product::class);
        $mapper = $this->createStub(ProductMapper::class);
        $mapper
            ->method('entityToModel')
            ->willReturnMap([
                [$productEntity1, $expectedProduct1],
                [$productEntity2, $expectedProduct2],
            ]);

        $repository = new ProductRepository($entityManager, $mapper);
        $results = $repository->findAll();

        self::assertSame([$expectedProduct1, $expectedProduct2], $results);
    }

    public function testFind(): void
    {
        $id = 123;

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('find')
            ->with($id)
            ->willReturn($productEntity);

        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);

        $expectedProduct = $this->createStub(Product::class);
        $mapper = $this->createStub(ProductMapper::class);
        $mapper
            ->method('entityToModel')
            ->with($productEntity)
            ->willReturn($expectedProduct);

        $repository = new ProductRepository($entityManager, $mapper);
        $foundProduct = $repository->find($id);

        self::assertSame($expectedProduct, $foundProduct);
    }

    public function testFindWhenNotFound(): void
    {
        $id = 123;

        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityManager, $mapper);
        $foundProduct = $repository->find($id);

        self::assertNull($foundProduct);
    }

    public function testCreate(): void
    {
        $product = $this->createStub(Product::class);

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(EntityRepository::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($productEntity);
        $entityManager
            ->expects($this->once())
            ->method('flush');

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

        $repository = new ProductRepository($entityManager, $mapper);
        $createdProduct = $repository->create($product);

        self::assertSame($finalProduct, $createdProduct);
    }

    public function testUpdate(): void
    {
        $product = $this->createStub(Product::class);
        $product->method('hasId')->willReturn(true);
        $product->method('getId')->willReturn(123);

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('find')
            ->with(123)
            ->willReturn($productEntity);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);
        $entityManager
            ->expects($this->once())
            ->method('flush');

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

        $repository = new ProductRepository($entityManager, $mapper);
        $updatedProduct = $repository->update($product);

        self::assertSame($finalProduct, $updatedProduct);
    }

    public function testUpdateWhenNoId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product ID is required for update');

        $product = $this->createStub(Product::class);
        $product->method('hasId')->willReturn(false);
        $product->method('getId')->willReturn(null);

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('find')
            ->with(123)
            ->willReturn($productEntity);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);
        $entityManager
            ->expects($this->never())
            ->method('flush');

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

        $repository = new ProductRepository($entityManager, $mapper);
        $updatedProduct = $repository->update($product);

        self::assertSame($finalProduct, $updatedProduct);
    }

    public function testUpdateWhenNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product does not exist');

        $product = $this->createStub(Product::class);
        $product->method('hasId')->willReturn(true);
        $product->method('getId')->willReturn(123);

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('find')
            ->with(123)
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);
        $entityManager
            ->expects($this->never())
            ->method('flush');

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

        $repository = new ProductRepository($entityManager, $mapper);
        $updatedProduct = $repository->update($product);

        self::assertSame($finalProduct, $updatedProduct);
    }

    public function testDelete(): void
    {
        $id = 123;

        $productEntity = $this->createStub(ProductEntity::class);
        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('find')
            ->with($id)
            ->willReturn($productEntity);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);
        $entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($productEntity);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityManager, $mapper);
        $repository->delete($id);
    }

    public function testDeleteWhenNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product does not exist');

        $id = 123;

        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);
        $entityManager
            ->expects($this->never())
            ->method('flush');

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityManager, $mapper);
        $repository->delete($id);
    }

    public function testExistsWhenFound(): void
    {
        $id = 123;

        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('count')
            ->with(['id' => $id])
            ->willReturn(1);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityManager, $mapper);
        $result = $repository->exists($id);

        self::assertTrue($result);
    }

    public function testExistsWhenNotFound(): void
    {
        $id = 123;

        $entityRepository = $this->createStub(EntityRepository::class);
        $entityRepository
            ->method('count')
            ->with(['id' => $id])
            ->willReturn(0);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('getRepository')
            ->with(ProductEntity::class)
            ->willReturn($entityRepository);

        $mapper = $this->createStub(ProductMapper::class);

        $repository = new ProductRepository($entityManager, $mapper);
        $result = $repository->exists($id);

        self::assertFalse($result);
    }
}
