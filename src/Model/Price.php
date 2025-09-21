<?php

namespace App\Model;

readonly class Price
{
    public function __construct(private int $amount)
    {
        if ($this->amount < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }
    }

    public function toInt(): int
    {
        return $this->amount;
    }
}
