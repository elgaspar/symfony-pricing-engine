<?php

namespace App\Model\Discount;

use App\Model\Cart\CartItem;
use App\Model\Price;

readonly class PercentageDiscountStrategy implements DiscountStrategyInterface
{
    public function __construct(private int $value)
    {
        if ($this->value < 1 || $this->value > 100) {
            throw new \InvalidArgumentException('Percentage discount value must be between 0 and 100');
        }
    }

    public function apply(CartItem $item): Price
    {
        $quantity = $item->getQuantity()->toInt();
        $basePrice = $item->getProduct()->getBasePrice()->toInt();


        return new Price(
            ($basePrice * (1 - $this->value / 100)) * $quantity
        );
    }

    public function toArray(): array
    {
        return [
            'type' => 'percentage',
            'value' => $this->value
        ];
    }
}
