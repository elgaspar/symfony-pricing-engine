<?php

namespace App\Tests\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Cart\Quantity;
use App\Model\Discount\PercentageDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class PercentageDiscountStrategyTest extends TestCase
{
    #[TestWith([0])]
    #[TestWith([1])]
    #[TestWith([50])]
    #[TestWith([99])]
    #[TestWith([100])]
    public function testValidPercentages(int $value): void
    {
        new PercentageDiscountStrategy($value);
        $this->expectNotToPerformAssertions();
    }

    #[TestWith([-1])]
    #[TestWith([101])]
    public function testInvalidPercentages(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new PercentageDiscountStrategy($value);
    }

    #[TestWith([1, 80])]
    #[TestWith([2, 160])]
    #[TestWith([3, 240])]
    public function testApplyWhenOneProduct(int $quantity, int $expectedFinalPrice): void
    {
        $discountStrategy = new PercentageDiscountStrategy(20);

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
        $discountStrategy = new PercentageDiscountStrategy(20);

        self::assertEquals(
            ['type' => 'percentage', 'value' => 20],
            $discountStrategy->toArray()
        );
    }
}
