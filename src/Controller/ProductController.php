<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Mapper\ProductMapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function list(ProductRepository $repository): JsonResponse
    {
        return $this->json($repository->findAll());
    }

    #[Route('/products/{id}', name: 'products_show', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET'])]
    public function show(ProductRepository $repository, int $id): JsonResponse
    {
        $product = $repository->find($id);

        if ($product === null) {
            throw $this->createNotFoundException();
        }

        return $this->json($product);
    }

    #[Route('/products', name: 'products_create', methods: ['POST'])]
    public function create(Request $request, ProductMapper $mapper, ProductRepository $repository): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $product = $mapper->arrayToModel($payload);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestException($e->getMessage());
        }

        $product = $repository->create($product);

        return $this->json(
            $product,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'products_show',
                    ['id' => $product->getId()]
                )
            ]
        );
    }

    #[Route('/products/{id}', name: 'products_update', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['PUT'])]
    public function update(int $id, Request $request, ProductMapper $mapper, ProductRepository $repository): JsonResponse
    {
        if (!$repository->exists($id)) {
            throw $this->createNotFoundException();
        }

        $payload = $request->getPayload()->all();

        try {
            $payload['id'] = $id;
            $product = $mapper->arrayToModel($payload);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestException($e->getMessage());
        }

        $product = $repository->update($product);

        return $this->json($product);
    }

    #[Route('/products/{id}', name: 'products_delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['DELETE'])]
    public function delete(int $id, ProductRepository $repository): JsonResponse
    {
        if (!$repository->exists($id)) {
            throw $this->createNotFoundException();
        }

        $repository->delete($id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
