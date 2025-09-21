<?php

namespace App\Model\Cart;


use App\Model\Product;

readonly class CartItem
{
    public function __construct(private Product $product, private Quantity $quantity)
    {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }
}
