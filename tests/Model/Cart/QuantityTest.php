<?php

namespace App\Tests\Model\Cart;

use App\Model\Cart\Quantity;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class QuantityTest extends TestCase
{
    #[TestWith([1])]
    #[TestWith([10])]
    public function testValidValues(int $value): void
    {
        $quantity = new Quantity($value);

        self::assertSame($value, $quantity->toInt());
    }

    #[TestWith([-1])]
    #[TestWith([0])]
    public function testInvalidValues(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Quantity($value);
    }
}
