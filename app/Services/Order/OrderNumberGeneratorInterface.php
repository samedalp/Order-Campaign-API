<?php

namespace App\Services\Order;

interface OrderNumberGeneratorInterface
{
    public function generate(): string;
}
