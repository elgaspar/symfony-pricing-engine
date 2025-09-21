<?php

namespace App\Tests\Model;

use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Price;
use App\Model\Product;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    #[TestWith([0])]
    #[TestWith([-1])]
    public function testInvalidId(int $id): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be a positive integer');

        new Product(
            $id,
            'Apple',
            new Price(100),
            new NoDiscountStrategy()
        );
    }

    #[DataProvider('invalidNameProvider')]
    public function testInvalidName(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name must be between 1 and 255 characters');

        new Product(
            123,
            $name,
            new Price(100),
            new NoDiscountStrategy()
        );
    }

    public static function invalidNameProvider(): array
    {
        return [
            'empty string' => [''],
            'whitespace' => [' '],
            'too long' => [str_repeat('a', 256)],
            'too long with multibyte characters' => [str_repeat('读', 256)],
        ];
    }

    #[DataProvider('validNameProvider')]
    public function testValidName(string $name): void
    {
        $product = new Product(
            123,
            $name,
            new Price(100),
            new NoDiscountStrategy()
        );

        self::assertSame($name, $product->getName());
    }

    public static function validNameProvider(): array
    {
        return [
            'min length' => ['1'],
            'max length' => [str_repeat('a', 255)],
            'max length with multibyte characters' => [str_repeat('读', 255)],
        ];
    }

    public function testNameIsTrimmed(): void
    {
        $product = new Product(
            123,
            '     Apple         ',
            new Price(100),
            new NoDiscountStrategy()
        );

        self::assertSame('Apple', $product->getName());
    }

    public function testGetters(): void
    {
        $discountStrategy = new NoDiscountStrategy();
        $product = new Product(
            123,
            'Apple',
            new Price(100),
            $discountStrategy
        );

        self::assertTrue($product->hasId());
        self::assertSame(123, $product->getId());
        self::assertSame('Apple', $product->getName());
        self::assertSame(100, $product->getBasePrice()->toInt());
        self::assertSame($discountStrategy, $product->getDiscountStrategy());
    }

    public function testGetIdAndHasIdWhenNoId(): void
    {
        $product = new Product(
            null,
            'Apple',
            new Price(100),
            new NoDiscountStrategy()
        );

        self::assertFalse($product->hasId());
        self::assertNull($product->getId());
    }

    public function testJsonSerialize(): void
    {
        $discountStrategy = new FixedDiscountStrategy(25);

        $product = new Product(
            123,
            'Apple',
            new Price(100),
            $discountStrategy
        );

        $expectedArray = [
            'id' => 123,
            'name' => 'Apple',
            'basePrice' => 100,
            'discountStrategy' => $discountStrategy->toArray()
        ];

        self::assertSame($expectedArray, $product->jsonSerialize());
    }
}
