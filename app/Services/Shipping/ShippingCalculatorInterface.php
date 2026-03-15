<?php

namespace App\Services\Shipping;

interface ShippingCalculatorInterface
{
    public function calculate(float $subtotal): float;
}
