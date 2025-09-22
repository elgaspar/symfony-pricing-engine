<?php

namespace App\Tests\Service\Factory;

use App\Model\Discount\BuyOneGetOneFreeDiscountStrategy;
use App\Model\Discount\FixedDiscountStrategy;
use App\Model\Discount\NoDiscountStrategy;
use App\Model\Discount\PercentageDiscountStrategy;
use App\Service\Factory\DiscountStrategyFactory;
use PHPUnit\Framework\TestCase;

class DiscountStrategyFactoryTest extends TestCase
{
    public function testFromArrayWhenTypeMissing(): void
    {
        $array = [];

        $factory = new DiscountStrategyFactory();
        $discountStrategy = $factory->fromArray($array);

        self::assertInstanceOf(NoDiscountStrategy::class, $discountStrategy);
    }

    public function testFromArrayWhenUnknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown discount type');

        $array = [
            'type' => 'unknown discount'
        ];

        $factory = new DiscountStrategyFactory();
        $factory->fromArray($array);
    }

    public function testFromArrayWhenNone(): void
    {
        $array = [
            'type' => 'none'
        ];

        $factory = new DiscountStrategyFactory();
        $discountStrategy = $factory->fromArray($array);

        self::assertInstanceOf(NoDiscountStrategy::class, $discountStrategy);
    }

    public function testFromArrayWhenFixed(): void
    {
        $array = [
            'type' => 'fixed',
            'value' => 10
        ];

        $factory = new DiscountStrategyFactory();
        $discountStrategy = $factory->fromArray($array);

        self::assertInstanceOf(FixedDiscountStrategy::class, $discountStrategy);
        self::assertSame(10, $discountStrategy->toArray()['value']);
    }

    public function testFromArrayWhenPercentage(): void
    {
        $array = [
            'type' => 'percentage',
            'value' => 20
        ];

        $factory = new DiscountStrategyFactory();
        $discountStrategy = $factory->fromArray($array);

        self::assertInstanceOf(PercentageDiscountStrategy::class, $discountStrategy);
        self::assertSame(20, $discountStrategy->toArray()['value']);
    }
    public function testFromArrayWhenBuyOneGetOneFree(): void
    {
        $array = [
            'type' => 'buyOneGetOneFree'
        ];

        $factory = new DiscountStrategyFactory();
        $discountStrategy = $factory->fromArray($array);

        self::assertInstanceOf(BuyOneGetOneFreeDiscountStrategy::class, $discountStrategy);
    }
}
