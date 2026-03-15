<?php

namespace App\Services\Order;

use App\Models\Order;

class OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    public function generate(): string
    {
        $count = Order::query()->count() + 1;
        return 'ORD-' . now()->format('Ymd') . '-' . str_pad((string)$count, 6, '0', STR_PAD_LEFT);
    }
}
