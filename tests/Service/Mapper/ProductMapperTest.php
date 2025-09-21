<?php

namespace App\Tests\Service\Mapper;

use App\Entity\ProductEntity;
use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use App\Service\Factory\DiscountStrategyFactory;
use App\Service\Mapper\ProductMapper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductMapperTest extends TestCase
{
    public function testArrayToModel(): void
    {
        $array = [
            'id' => 123,
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $model = $mapper->arrayToModel($array);

        self::assertSame(123, $model->getId());
        self::assertSame('Apple', $model->getName());
        self::assertSame(100, $model->getBasePrice()->toInt());
        self::assertEquals(new FixedDiscountStrategy(1), $model->getDiscountStrategy());
    }

    public function testArrayToModelWhenNoId(): void
    {
        $array = [
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $model = $mapper->arrayToModel($array);

        self::assertNull($model->getId());
        self::assertSame('Apple', $model->getName());
        self::assertSame(100, $model->getBasePrice()->toInt());
        self::assertEquals(new FixedDiscountStrategy(1), $model->getDiscountStrategy());
    }

    public function testArrayToModelWhenNoIsNull(): void
    {
        $array = [
            'id' => null,
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $model = $mapper->arrayToModel($array);

        self::assertNull($model->getId());
        self::assertSame('Apple', $model->getName());
        self::assertSame(100, $model->getBasePrice()->toInt());
        self::assertEquals(new FixedDiscountStrategy(1), $model->getDiscountStrategy());
    }

    public function testArrayToModelWhenIdInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid id type');

        $array = [
            'id' => 'not an integer',
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $mapper->arrayToModel($array);
    }

    public function testArrayToModelWhenNameMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing data');

        $array = [
            'id' => 123,
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $mapper->arrayToModel($array);
    }

    public function testArrayToModelWhenNameInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid data type');

        $array = [
            'id' => 123,
            'name' => ['foo'],
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $mapper->arrayToModel($array);
    }

    public function testArrayToModelWhenPriceMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing data');

        $array = [
            'id' => 123,
            'name' => 'Apple',
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $mapper->arrayToModel($array);
    }

    public function testArrayToModelWhenPriceInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid data type');

        $array = [
            'id' => 123,
            'name' => 'Apple',
            'basePrice' => 'not an integer',
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $mapper->arrayToModel($array);
    }

    public function testArrayToModelWhenDiscountMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing data');

        $array = [
            'id' => 123,
            'name' => 'Apple',
            'basePrice' => 100
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $mapper->arrayToModel($array);
    }

    public function testArrayToModelWhenDiscountInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown discount type');

        $array = [
            'id' => 123,
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'not existing discount type',
                'value' => 1
            ]
        ];

        $mapper = new ProductMapper(new DiscountStrategyFactory());

        $mapper->arrayToModel($array);
    }

    public function testEntityToModel(): void
    {
        $entity = $this->createStub(ProductEntity::class);
        $entity->method('getId')->willReturn(123);
        $entity->method('getName')->willReturn('Apple');
        $entity->method('getBasePrice')->willReturn(100);
        $entity->method('getDiscountStrategy')->willReturn([
            'type' => 'fixed',
            'value' => 1
        ]);

        $mapper = new ProductMapper(new DiscountStrategyFactory());
        $model = $mapper->entityToModel($entity);

        self::assertSame(123, $model->getId());
        self::assertSame('Apple', $model->getName());
        self::assertSame(100, $model->getBasePrice()->toInt());
        self::assertEquals(new FixedDiscountStrategy(1), $model->getDiscountStrategy());
    }

    public function testModelToEntity(): void
    {
        $product = new Product(
            123,
            'Apple',
            new Price(100),
            new FixedDiscountStrategy(1)
        );

        $mapper = new ProductMapper(new DiscountStrategyFactory());
        $entity = $mapper->modelToEntity($product);

        self::assertSame('Apple', $entity->getName());
        self::assertSame(100, $entity->getBasePrice());
        self::assertSame(['type' => 'fixed', 'value' => 1], $entity->getDiscountStrategy());
    }

    public function testModelToEntityWhenEntityGiven(): void
    {
        $product = new Product(
            123,
            'Apple',
            new Price(100),
            new FixedDiscountStrategy(1)
        );

        $givenEntity = new ProductEntity();
        (new \ReflectionClass($givenEntity))
            ->getProperty('id')
            ->setValue($givenEntity, 456);

        $mapper = new ProductMapper(new DiscountStrategyFactory());
        $entity = $mapper->modelToEntity($product, $givenEntity);

        self::assertSame(456, $entity->getId());
        self::assertSame('Apple', $entity->getName());
        self::assertSame(100, $entity->getBasePrice());
        self::assertSame(['type' => 'fixed', 'value' => 1], $entity->getDiscountStrategy());
    }
}
