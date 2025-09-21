<?php

namespace App\Model;

use App\Model\Discount\DiscountStrategyInterface;

readonly class Product implements \JsonSerializable
{
    private string $name;

    public function __construct(
        private ?int $id,
        string $name,
        private Price $basePrice,
        private DiscountStrategyInterface $discountStrategy
    ) {
        if ($id !== null && $id <= 0) {
            throw new \InvalidArgumentException('ID must be a positive integer');
        }

        $this->name = trim($name);
        if ($this->name === '' || mb_strlen($this->name) > 255) {
            throw new \InvalidArgumentException('Name must be between 1 and 255 characters');
        }
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
