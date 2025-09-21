<?php

namespace App\Tests\Model;

use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGetters(): void
    {
        $discountStrategy = new NoDiscountStrategy();
        $product = new Product(
            123,
            'Apple',
            new Price(100),
            $discountStrategy
        );

        self::assertTrue($product->hasId());
        self::assertSame(123, $product->getId());
        self::assertSame('Apple', $product->getName());
        self::assertSame(100, $product->getBasePrice()->toInt());
        self::assertSame($discountStrategy, $product->getDiscountStrategy());
    }

    public function testGetIdAndHasIdWhenNoId(): void
    {
        $product = new Product(
            null,
            'Apple',
            new Price(100),
            new NoDiscountStrategy()
        );

        self::assertFalse($product->hasId());
        self::assertNull($product->getId());
    }

    public function testJsonSerialize(): void
    {
        $discountStrategy = new FixedDiscountStrategy(25);

        $product = new Product(
            123,
            'Apple',
            new Price(100),
            $discountStrategy
        );

        $expectedArray = [
            'id' => 123,
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => $discountStrategy->toArray()
        ];

        self::assertSame($expectedArray, $product->jsonSerialize());
    }
}
