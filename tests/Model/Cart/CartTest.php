<?php

namespace App\Tests\Model\Cart;

use App\Model\Cart\Cart;
use App\Model\Cart\CartItem;
use App\Model\Cart\Quantity;
use App\Model\Product;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    public function testValidItems(): void
    {
        $cartItem1 = $this->createStub(CartItem::class);
        $cartItem2 = $this->createStub(CartItem::class);
        new Cart([$cartItem1, $cartItem2]);

        $this->expectNotToPerformAssertions();
    }

    public function testInvalidItems(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $cartItem = new \DateTime();
        new Cart([$cartItem]);
    }

    public function testIterator(): void
    {
        $cartItem1 = $this->createStub(CartItem::class);
        $cartItem2 = $this->createStub(CartItem::class);
        $cart = new Cart([$cartItem1, $cartItem2]);

        $iteratedItems = [];
        foreach ($cart as $item) {
            $iteratedItems[] = $item;
        }

        self::assertSame(
            [$cartItem1, $cartItem2],
            $iteratedItems
        );
    }
}
