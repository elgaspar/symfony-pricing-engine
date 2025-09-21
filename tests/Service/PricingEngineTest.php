<?php

namespace App\Tests\Service;

use App\Model\Cart\Cart;
use App\Model\Cart\CartItem;
use App\Model\Cart\Quantity;
use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Discount\PercentageDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use App\Service\PricingEngine;
use PHPUnit\Framework\TestCase;

class PricingEngineTest extends TestCase
{
    public function testCalculateTotalWhenEmptyCart(): void
    {
        $cart = new Cart([]);

        $pricingEngine = new PricingEngine();
        $finalPrice = $pricingEngine->calculateTotal($cart);

        $this->assertEquals(0, $finalPrice->toInt());
    }

    public function testCalculateTotalWhenMultipleItems(): void
    {
        $cart = new Cart([
            new CartItem(
                new Product(
                    null,
                    'Apple',
                    new Price(100),
                    new PercentageDiscountStrategy(10)
                ),
                new Quantity(3)
            ),
            new CartItem(
                new Product(
                    null,
                    'Orange',
                    new Price(10),
                    new FixedDiscountStrategy(1)
                ),
                new Quantity(2)
            )
        ]);

        $pricingEngine = new PricingEngine();
        $finalPrice = $pricingEngine->calculateTotal($cart);

        $this->assertEquals(288, $finalPrice->toInt());
    }
}
