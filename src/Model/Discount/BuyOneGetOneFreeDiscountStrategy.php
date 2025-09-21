<?php

namespace App\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Price;

readonly class BuyOneGetOneFreeDiscountStrategy implements DiscountStrategyInterface
{
    public function apply(CartItem $item): Price
    {
        $quantity = $item->getQuantity()->toInt();
        $basePrice = $item->getProduct()->getBasePrice()->toInt();

        return new Price(
            $basePrice * ceil($quantity / 2)
        );
    }

    public function toArray(): array
    {
        return [
            'type' => 'buyOneGetOneFree'
        ];
    }
}
