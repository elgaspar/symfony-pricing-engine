<?php

namespace App\Tests\Controller;

use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    public function testCheckout(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var ProductRepository $repository */
        $repository = $container->get(ProductRepository::class);
        $product1 = $repository->create(new Product(null, 'Apple', new Price(100), new NoDiscountStrategy()));
        $product2 = $repository->create(new Product(null, 'Peach', new Price(200), new FixedDiscountStrategy(20)));

        $client->request('POST', '/checkout', [], [], [], json_encode([
            'items' => [
                [
                    'productId' => $product1->getId(),
                    'quantity' => 5,
                ],
                [
                    'productId' => $product2->getId(),
                    'quantity' => 3,
                ],
            ]
        ], JSON_THROW_ON_ERROR));

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(['finalPrice' => 1040], $response);

        $repository->delete($product1->getId());
        $repository->delete($product2->getId());
    }

    public function testCheckoutWhenBadRequest(): void
    {
        $client = static::createClient();

        $client->request('POST', '/checkout', [], [], [], json_encode([
            'items' => [
                [
                    'productId' => 'invalid id',
                    'quantity' => 5,
                ]
            ]
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testCheckoutWhenProductNotExists(): void
    {
        $client = static::createClient();

        $client->request('POST', '/checkout', [], [], [], json_encode([
            'items' => [
                [
                    'productId' => '99999',
                    'quantity' => 5,
                ]
            ]
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
