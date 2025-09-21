<?php

namespace App\Model;

use App\Model\Discount\DiscountStrategyInterface;

readonly class Product implements \JsonSerializable
{
    public function __construct(
        private ?int $id,
        private string $name,
        private Price $basePrice,
        private DiscountStrategyInterface $discountStrategy
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function hasId(): bool
    {
        return $this->id !== null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBasePrice(): Price
    {
        return $this->basePrice;
    }

    public function getDiscountStrategy(): DiscountStrategyInterface
    {
        return $this->discountStrategy;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'basePrice' => $this->basePrice->toInt(),
            'discountStrategy' => $this->getDiscountStrategy()->toArray()
        ];
    }
}
