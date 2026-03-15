<?php

namespace App\Services\Order;

use App\Models\Order;

interface CreateOrderServiceInterface
{
    /**
     * @throws \Exception|\Throwable
     */
    public function createOrderWithCampaign(array $payload): Order;
}
