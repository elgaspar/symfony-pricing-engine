<?php

namespace App\Model\Cart;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, CartItem>
 */
readonly class Cart implements IteratorAggregate
{
    public function __construct(private array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof CartItem) {
                throw new InvalidArgumentException('All items must be instances of CartItem');
            }
        }
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
