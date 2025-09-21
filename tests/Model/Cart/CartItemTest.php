<?php

namespace App\Tests\Model\Cart;

use App\Model\Cart\CartItem;
use App\Model\Cart\Quantity;
use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{
    public function testGetters(): void
    {
        $product = $this->createStub(Product::class);
        $quantity = new Quantity(2);

        $cartItem = new CartItem($product, $quantity);

        self::assertSame($product, $cartItem->getProduct());
        self::assertSame($quantity, $cartItem->getQuantity());
    }
}
