<?php

namespace App\Services\Shipping;

class ShippingCalculator implements ShippingCalculatorInterface
{
    private const FREE_SHIPPING_THRESHOLD = 50;
    private const SHIPPING_COST = 10;

    public function calculate(float $subtotal): float
    {
        return $subtotal >= self::FREE_SHIPPING_THRESHOLD
            ? 0.0
            : self::SHIPPING_COST;
    }
}
