<?php

namespace App\Service\Factory;

use App\Model\Cart\Cart;
use App\Model\Cart\CartItem;
use App\Model\Cart\Quantity;
use App\Repository\ProductRepository;

class CartFactory
{
    public function __construct(private ProductRepository $repository)
    {
    }

    public function fromArray(array $values): Cart
    {
        if (!isset($values['items']) || !is_array($values['items'])) {
            throw new \InvalidArgumentException('Invalid cart item data');
        }

        $cartItems = [];
        foreach ($values['items'] as $item) {
            if (
                !isset($item['productId'], $item['quantity']) ||
                !is_numeric($item['productId']) ||
                !is_numeric($item['quantity'])
            ) {
                throw new \InvalidArgumentException('Invalid cart item data');
            }

            $product = $this->repository->find((int) $item['productId']);

            if ($product === null) {
                throw new \InvalidArgumentException(sprintf('Product with ID %d not found', (int) $item['productId']));
            }

            $cartItems[] = new CartItem(
                $product,
                new Quantity((int) $item['quantity'])
            );
        }

        return new Cart($cartItems);
    }
}
