<?php

namespace App\Tests\Model;

use App\Model\Price;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    #[TestWith([0])]
    #[TestWith([1])]
    #[TestWith([999])]
    public function testValidValues(int $value): void
    {
        $price = new Price($value);

        self::assertSame($value, $price->toInt());
    }

    public function testInvalidValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Price(-1);
    }
}
