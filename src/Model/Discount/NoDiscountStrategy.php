<?php

namespace App\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Price;

readonly class NoDiscountStrategy implements DiscountStrategyInterface
{
    public function apply(CartItem $item): Price
    {
        $quantity = $item->getQuantity()->toInt();
        $basePrice = $item->getProduct()->getBasePrice()->toInt();

        return new Price($basePrice * $quantity);
    }

    public function toArray(): array
    {
        return [];
    }
}
