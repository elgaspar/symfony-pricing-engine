<?php

namespace App\Controller;

use App\Service\Factory\CartFactory;
use App\Service\PricingEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout', methods: ['POST'])]
    public function checkout(Request $request, CartFactory $cartFactory, PricingEngine $pricingEngine): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $cart = $cartFactory->fromArray($payload);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        $finalPrice = $pricingEngine->calculateTotal($cart);

        return $this->json(['finalPrice' => $finalPrice->toInt()]);
    }
}
