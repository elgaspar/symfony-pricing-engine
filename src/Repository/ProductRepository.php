<?php

namespace App\Repository;

use App\Entity\ProductEntity;
use App\Model\Product;
use App\Service\Mapper\ProductMapper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

class ProductRepository
{
    private EntityRepository $entityRepository;

    public function __construct(private EntityManagerInterface $entityManager, private ProductMapper $mapper)
    {
        $this->entityRepository = $entityManager->getRepository(ProductEntity::class);
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

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->mapper->entityToModel($entity);
    }

    public function update(Product $product): Product
    {
        if (!$product->hasId()) {
            throw new InvalidArgumentException('Product ID is required for update');
        }

        $existingEntity = $this->entityRepository->find($product->getId());

        if ($existingEntity === null) {
            throw new InvalidArgumentException('Product does not exist');
        }

        $entity = $this->mapper->modelToEntity($product, $existingEntity);
        $this->entityManager->flush();

        return $this->mapper->entityToModel($entity);
    }

    public function delete(int $id): void
    {
        $existingEntity = $this->entityRepository->find($id);

        if ($existingEntity === null) {
            throw new InvalidArgumentException('Product does not exist');
        }

        $this->entityManager->remove($existingEntity);
        $this->entityManager->flush();
    }

    public function exists(int $id): bool
    {
        return $this->entityRepository->count(['id' => $id]) > 0;
    }
}
