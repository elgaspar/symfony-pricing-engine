<?php

namespace App\Repository;

use App\Model\Product;
use App\Service\Mapper\ProductMapper;
use InvalidArgumentException;

class ProductRepository
{
    public function __construct(private ProductEntityRepository $entityRepository, private ProductMapper $mapper)
    {
    }

    public function findAll(): array
    {
        $entities = $this->entityRepository->findAll();

        $products = [];
        foreach ($entities as $entity) {
            $products[] = $this->mapper->entityToModel($entity);
        }

        return $products;
    }

    public function find(int $id): ?Product
    {
        $entity = $this->entityRepository->find($id);

        if ($entity === null) {
            return null;
        }

        return $this->mapper->entityToModel($entity);
    }

    public function create(Product $product): Product
    {
        $entity = $this->mapper->modelToEntity($product);
        $this->entityRepository->save($entity);

        return $this->mapper->entityToModel($entity);
    }

    public function update(Product $product): Product
    {
        $existingEntity = $this->entityRepository->find($product->getId());

        if ($existingEntity === null) {
            throw new InvalidArgumentException('Product does not exist');
        }

        $entity = $this->mapper->modelToEntity($product, $existingEntity);
        $this->entityRepository->save($entity);

        return $this->mapper->entityToModel($entity);
    }

    public function delete(int $id): void
    {
        if (!$this->entityRepository->exists($id)) {
            throw new InvalidArgumentException('Product does not exist');
        }

        $this->entityRepository->delete($id);
    }

    public function exists(int $id): bool
    {
        return $this->entityRepository->exists($id);
    }
}
