<?php

namespace App\Tests\Service\Factory;

use App\Model\Cart\CartItem;
use App\Model\Product;
use App\Repository\ProductRepository;
use App\Service\Factory\CartFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CartFactoryTest extends TestCase
{
    public function testFromArrayWhenItemsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid cart item data');

        $array = [
            'foo' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ]
            ],
        ];

        $repository = $this->createStub(ProductRepository::class);
        $repository->method('find')->willReturn($this->createStub(Product::class));

        $factory = new CartFactory($repository);
        $factory->fromArray($array);
    }

    public function testFromArrayWhenProductIdMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid cart item data');

        $array = [
            'items' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ],
                [
                    'quantity' => 5,
                ],
            ],
        ];

        $repository = $this->createStub(ProductRepository::class);
        $repository->method('find')->willReturn($this->createStub(Product::class));

        $factory = new CartFactory($repository);
        $factory->fromArray($array);
    }

    public function testFromArrayWhenProductIdInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid cart item data');

        $array = [
            'items' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ],
                [
                    'productId' => 'foo',
                    'quantity' => 5,
                ],
            ],
        ];

        $repository = $this->createStub(ProductRepository::class);
        $repository->method('find')->willReturn($this->createStub(Product::class));

        $factory = new CartFactory($repository);
        $factory->fromArray($array);
    }

    public function testFromArrayWhenQuantityMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid cart item data');

        $array = [
            'items' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ],
                [
                    'productId' => 13,
                ],
            ],
        ];

        $repository = $this->createStub(ProductRepository::class);
        $repository->method('find')->willReturn($this->createStub(Product::class));

        $factory = new CartFactory($repository);
        $factory->fromArray($array);
    }

    public function testFromArrayWhenQuantityInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid cart item data');

        $array = [
            'items' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ],
                [
                    'productId' => 13,
                    'quantity' => 'foo',
                ],
            ],
        ];

        $repository = $this->createStub(ProductRepository::class);
        $repository->method('find')->willReturn($this->createStub(Product::class));

        $factory = new CartFactory($repository);
        $factory->fromArray($array);
    }


    public function testFromArrayWhenEmpty(): void
    {
        $array = [
            'items' => [],
        ];

        $repository = $this->createStub(ProductRepository::class);
        $repository->method('find')->willReturn($this->createStub(Product::class));

        $factory = new CartFactory($repository);
        $cart = $factory->fromArray($array);

        $cartItems = [];
        foreach ($cart as $item) {
            $cartItems[] = $item;
        }

        self::assertEmpty($cartItems);
    }

    public function testFromArrayWhenOneItem(): void
    {
        $array = [
            'items' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ]
            ],
        ];

        $expectedProduct = $this->createStub(Product::class);
        $repository = $this->createStub(ProductRepository::class);
        $repository
            ->method('find')
            ->with(111)
            ->willReturn($expectedProduct);

        $factory = new CartFactory($repository);
        $cart = $factory->fromArray($array);

        $cartItems = [];
        foreach ($cart as $item) {
            $cartItems[] = $item;
        }

        self::assertCount(1, $cartItems);
        self::assertInstanceOf(CartItem::class, $cartItems[0]);
        self::assertSame($expectedProduct, $cartItems[0]->getProduct());
        self::assertSame(2, $cartItems[0]->getQuantity()->toInt());
    }

    public function testFromArrayWhenMultipleItems(): void
    {
        $array = [
            'items' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ],
                [
                    'productId' => 13,
                    'quantity' => 5,
                ]
            ],
        ];

        $expectedProduct1 = $this->createStub(Product::class);
        $expectedProduct2 = $this->createStub(Product::class);
        $repository = $this->createStub(ProductRepository::class);
        $repository
            ->method('find')
            ->willReturnMap([
                [111, $expectedProduct1],
                [13, $expectedProduct2],
            ]);

        $factory = new CartFactory($repository);
        $cart = $factory->fromArray($array);

        $cartItems = [];
        foreach ($cart as $item) {
            $cartItems[] = $item;
        }

        self::assertCount(2, $cartItems);

        self::assertInstanceOf(CartItem::class, $cartItems[0]);
        self::assertSame($expectedProduct1, $cartItems[0]->getProduct());
        self::assertSame(2, $cartItems[0]->getQuantity()->toInt());

        self::assertInstanceOf(CartItem::class, $cartItems[1]);
        self::assertSame($expectedProduct2, $cartItems[1]->getProduct());
        self::assertSame(5, $cartItems[1]->getQuantity()->toInt());
    }

    public function testFromArrayWhenContainsNotExistingProduct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product with ID 111 not found');

        $array = [
            'items' => [
                [
                    'productId' => 111,
                    'quantity' => 2,
                ]
            ],
        ];

        $repository = $this->createStub(ProductRepository::class);
        $repository
            ->method('find')
            ->willReturn(null);

        $factory = new CartFactory($repository);
        $factory->fromArray($array);
    }
}
