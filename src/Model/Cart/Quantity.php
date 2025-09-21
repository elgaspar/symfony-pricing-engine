<?php

namespace App\Model\Cart;

readonly class Quantity
{
    public function __construct(private int $quantity)
    {
        if ($this->quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1');
        }
    }

    public function toInt(): int
    {
        return $this->quantity;
    }
}
