<?php

namespace App\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Price;

readonly class FixedDiscountStrategy implements DiscountStrategyInterface
{
    public function __construct(private int $value)
    {
        if ($this->value <= 0) {
            throw new \InvalidArgumentException('Fixed discount value must be positive');
        }
    }

    public function apply(CartItem $item): Price
    {
        $quantity = $item->getQuantity()->toInt();
        $basePrice = $item->getProduct()->getBasePrice()->toInt();

        return new Price(
            ($basePrice - $this->value) * $quantity
        );
    }

    public function toArray(): array
    {
        return [
            'type' => 'fixed',
            'value' => $this->value
        ];
    }
}
