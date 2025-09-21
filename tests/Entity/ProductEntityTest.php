<?php

namespace App\Tests\Entity;

use App\Entity\ProductEntity;
use PHPUnit\Framework\TestCase;

class ProductEntityTest extends TestCase
{
    public function testGetters(): void
    {
        $entity = new ProductEntity();
        (new \ReflectionClass($entity))
            ->getProperty('id')
            ->setValue($entity, 456);

        $entity->setName('Test Product');
        $entity->setBasePrice(1000);
        $entity->setDiscountStrategy(['type' => 'none']);

        self::assertSame(456, $entity->getId());
        self::assertSame('Test Product', $entity->getName());
        self::assertSame(1000, $entity->getBasePrice());
        self::assertSame(['type' => 'none'], $entity->getDiscountStrategy());
    }
}
