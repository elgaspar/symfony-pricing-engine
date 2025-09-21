<?php

namespace App\Tests\Repository;

use App\Entity\ProductEntity;
use App\Repository\ProductEntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductEntityRepositoryTest extends KernelTestCase
{
    public function testSave(): void
    {
        $entity = new ProductEntity();
        $entity->setName('Apple');
        $entity->setBasePrice(100);
        $entity->setDiscountStrategy(['type' => 'none']);

        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(ProductEntityRepository::class);
        self::assertInstanceOf(ProductEntityRepository::class, $repository);

        $repository->save($entity);
        self::assertTrue($repository->exists($entity->getId()));

        $repository->delete($entity->getId());
    }

    public function testDeleteWhenExists(): void
    {
        $entity = new ProductEntity();
        $entity->setName('Apple');
        $entity->setBasePrice(100);
        $entity->setDiscountStrategy(['type' => 'none']);

        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(ProductEntityRepository::class);

        self::assertInstanceOf(ProductEntityRepository::class, $repository);

        $repository->save($entity);
        $id = $entity->getId();
        $repository->delete($entity->getId());

        self::assertFalse($repository->exists($id));
    }

    public function testDeleteWhenNotExists(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(ProductEntityRepository::class);

        self::assertInstanceOf(ProductEntityRepository::class, $repository);

        $repository->delete(99999);
    }

    public function testExistsWhenTrue(): void
    {
        $entity = new ProductEntity();
        $entity->setName('Apple');
        $entity->setBasePrice(100);
        $entity->setDiscountStrategy(['type' => 'none']);

        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(ProductEntityRepository::class);
        self::assertInstanceOf(ProductEntityRepository::class, $repository);

        $repository->save($entity);
        self::assertTrue($repository->exists($entity->getId()));

        $repository->delete($entity->getId());
    }

    public function testExistWhenFalse(): void
    {
        $entity = new ProductEntity();
        $entity->setName('Apple');
        $entity->setBasePrice(100);
        $entity->setDiscountStrategy(['type' => 'none']);

        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(ProductEntityRepository::class);
        self::assertInstanceOf(ProductEntityRepository::class, $repository);

        self::assertFalse($repository->exists(99999));
    }
}
