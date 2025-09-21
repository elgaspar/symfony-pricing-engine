<?php

namespace App\Entity;

use App\Repository\ProductEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductEntityRepository::class)]
#[ORM\Table(name: "product")]
class ProductEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $basePrice;

    #[ORM\Column]
    private array $discountStrategy;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBasePrice(): int
    {
        return $this->basePrice;
    }

    public function setBasePrice(int $basePrice): static
    {
        $this->basePrice = $basePrice;

        return $this;
    }

    public function getDiscountStrategy(): array
    {
        return $this->discountStrategy;
    }

    public function setDiscountStrategy(array $discountStrategy): static
    {
        $this->discountStrategy = $discountStrategy;

        return $this;
    }
}
