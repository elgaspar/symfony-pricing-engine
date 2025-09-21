<?php

namespace App\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Price;

interface DiscountStrategyInterface
{
    public function apply(CartItem $item): Price;
    public function toArray(): array;
}
