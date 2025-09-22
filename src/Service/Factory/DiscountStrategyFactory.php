<?php

namespace App\Service\Factory;

use App\Model\Discount\BuyOneGetOneFreeDiscountStrategy;
use App\Model\Discount\DiscountStrategyInterface;
use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Discount\PercentageDiscountStrategy;
use InvalidArgumentException;

class DiscountStrategyFactory
{
    public function fromArray(array $data): DiscountStrategyInterface
    {
        if (!isset($data['type'])) {
            return new NoDiscountStrategy();
        }

        $type = (string)$data['type'];

        return match ($type) {
            'none' => new NoDiscountStrategy(),
            'fixed' => new FixedDiscountStrategy($data['value'] ?? 0),
            'percentage' => new PercentageDiscountStrategy($data['value'] ?? 0),
            'buyOneGetOneFree' => new BuyOneGetOneFreeDiscountStrategy(),
            default => throw new InvalidArgumentException("Unknown discount type"),
        };
    }
}
