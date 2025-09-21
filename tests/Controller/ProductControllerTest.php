<?php

namespace App\Tests\Controller;

use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductControllerTest extends WebTestCase
{
    public function testList(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var ProductRepository $repository */
        $repository = $container->get(ProductRepository::class);
        $product1 = $repository->create(new Product(null, 'Apple', new Price(100), new NoDiscountStrategy()));
        $product2 = $repository->create(new Product(null, 'Peach', new Price(200), new FixedDiscountStrategy(20)));


        $client->request('GET', '/products');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $expectedArray = [
            [
                'id' => $product1->getId(),
                'name' => 'Apple',
                'basePrice' => 100,
                'discountStrategy' => [],
            ],
            [
                'id' => $product2->getId(),
                'name' => 'Peach',
                'basePrice' => 200,
                'discountStrategy' => [
                    'type' => 'fixed',
                    'value' => 20,
                ],
            ],
        ];
        self::assertSame($expectedArray, $response);

        $repository->delete($product1->getId());
        $repository->delete($product2->getId());
    }

    public function testListWhenNoProducts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame([], $response);
    }

    public function testShow(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var ProductRepository $repository */
        $repository = $container->get(ProductRepository::class);
        $product = $repository->create(new Product(null, 'Apple', new Price(100), new FixedDiscountStrategy(22)));

        $client->request('GET', '/products/' . $product->getId());

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $expectedArray = [
            'id' => $product->getId(),
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 22,
            ],
        ];
        self::assertSame($expectedArray, $response);

        $repository->delete($product->getId());
    }

    public function testShowWhenNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/products/99999');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testCreate(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var ProductRepository $repository */
        $repository = $container->get(ProductRepository::class);

        $client->request('POST', '/products', [], [], [], json_encode([
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 22,
            ],
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($response);
        self::assertArrayHasKey('id', $response);

        self::assertArrayHasKey('name', $response);
        self::assertSame('Apple', $response['name']);

        self::assertArrayHasKey('basePrice', $response);
        self::assertSame(100, $response['basePrice']);

        self::assertArrayHasKey('discountStrategy', $response);
        self::assertIsArray($response['discountStrategy']);
        self::assertArrayHasKey('type', $response['discountStrategy']);
        self::assertArrayHasKey('value', $response['discountStrategy']);
        self::assertSame(['type' => 'fixed', 'value' => 22], $response['discountStrategy']);

        $repository->delete($response['id']);
    }

    public function testCreateWhenBadRequest(): void
    {
        $client = static::createClient();

        $client->request('POST', '/products', [], [], [], json_encode([
            'name' => ['array instead of string'],
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 22,
            ],
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testUpdate(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var ProductRepository $repository */
        $repository = $container->get(ProductRepository::class);
        $product = $repository->create(new Product(null, 'Apple', new Price(100), new FixedDiscountStrategy(22)));

        $client->request('PUT', '/products/' . $product->getId(), [], [], [], json_encode([
            'name' => 'Orange',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 22,
            ],
        ], JSON_THROW_ON_ERROR));

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $expectedArray = [
            'id' => $product->getId(),
            'name' => 'Orange',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 22,
            ],
        ];
        self::assertSame($expectedArray, $response);

        $repository->delete($product->getId());
    }

    public function testUpdateWhenNotFound(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/products/99999', [], [], [], json_encode([
            'name' => 'Orange',
            'basePrice' => 100,
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 22,
            ],
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testUpdateWhenBadRequest(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var ProductRepository $repository */
        $repository = $container->get(ProductRepository::class);
        $product = $repository->create(new Product(null, 'Apple', new Price(100), new FixedDiscountStrategy(22)));

        $client->request('PUT', '/products/' . $product->getId(), [], [], [], json_encode([
            'name' => 'Orange',
            'basePrice' => 'invalid price value',
            'discountStrategy' => [
                'type' => 'fixed',
                'value' => 22,
            ],
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $repository->delete($product->getId());
    }

}
