<?php

namespace App\Service;

use App\Model\Cart\Cart;
use App\Model\Price;

class PricingEngine
{
    public function calculateTotal(Cart $cart): Price
    {
        $finalPrice = 0;

        foreach ($cart as $item) {
            $discountStrategy = $item->getProduct()->getDiscountStrategy();
            $finalPrice += $discountStrategy->apply($item)->toInt();
        }

        return new Price($finalPrice);
    }
}
