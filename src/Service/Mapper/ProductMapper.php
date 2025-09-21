<?php

namespace App\Service\Mapper;

use App\Entity\ProductEntity;
use App\Model\Price;
use App\Model\Product;
use App\Service\Factory\DiscountStrategyFactory;

class ProductMapper
{
    public function __construct(private DiscountStrategyFactory $discountStrategyFactory)
    {
    }

    public function arrayToModel(array $data): Product
    {
        if (!isset($data['name'], $data['basePrice'], $data['discountStrategy'])) {
            throw new \InvalidArgumentException('Missing data');
        }

        if (!is_string($data['name']) || !is_numeric($data['basePrice']) || !is_array($data['discountStrategy'])) {
            throw new \InvalidArgumentException('Invalid data type');
        }

        if (isset($data['id']) && !is_int($data['id']) && $data['id'] !== null) {
            throw new \InvalidArgumentException('Invalid id type');
        }

        return new Product(
            $data['id'] ?? null,
            $data['name'],
            new Price((int) $data['basePrice']),
            $this->discountStrategyFactory->fromArray($data['discountStrategy'])
        );
    }

    public function entityToModel(ProductEntity $entity): Product
    {
        return new Product(
            $entity->getId(),
            $entity->getName(),
            new Price($entity->getBasePrice()),
            $this->discountStrategyFactory->fromArray($entity->getDiscountStrategy())
        );
    }

    public function modelToEntity(Product $product, ?ProductEntity $entity = null): ProductEntity
    {
        $entity ??= new ProductEntity();
        $entity->setName($product->getName());
        $entity->setBasePrice($product->getBasePrice()->toInt());
        $entity->setDiscountStrategy($product->getDiscountStrategy()->toArray());

        return $entity;
    }
}
