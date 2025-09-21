<?php

namespace App\Tests\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Cart\Quantity;
use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class FixedDiscountStrategyTest extends TestCase
{
    #[TestWith([1])]
    #[TestWith([50])]
    public function testValidPercentages(int $value): void
    {
        new FixedDiscountStrategy($value);
        $this->expectNotToPerformAssertions();
    }

    #[TestWith([-1])]
    #[TestWith([0])]
    public function testInvalidPercentages(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FixedDiscountStrategy($value);
    }

    #[TestWith([1, 90])]
    #[TestWith([1, 90])]
    #[TestWith([2, 180])]
    #[TestWith([3, 270])]
    public function testApply(int $quantity, int $expectedFinalPrice): void
    {
        $discountStrategy = new FixedDiscountStrategy(10);

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
        $discountStrategy = new FixedDiscountStrategy(10);

        self::assertEquals(
            ['type' => 'fixed', 'value' => 10],
            $discountStrategy->toArray()
        );
    }
}
