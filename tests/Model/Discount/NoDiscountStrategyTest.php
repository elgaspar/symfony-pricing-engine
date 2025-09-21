<?php

namespace App\Tests\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Cart\Quantity;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NoDiscountStrategyTest extends TestCase
{
    #[TestWith([1, 100])]
    #[TestWith([2, 200])]
    #[TestWith([3, 300])]
    public function testApply(int $quantity, int $expectedFinalPrice): void
    {
        $discountStrategy = new NoDiscountStrategy();

        $product = new Product(
            123,
            'Apple',
            new Price(100),
            $discountStrategy
        );
        $cartItem = new CartItem($product, new Quantity($quantity));

        $finalPrice = $discountStrategy->apply($cartItem);

        self::assertSame($expectedFinalPrice, $finalPrice->toInt());
    }

    public function testToArray(): void
    {
        $discountStrategy = new NoDiscountStrategy();

        self::assertEquals(
            [],
            $discountStrategy->toArray()
        );
    }
}
