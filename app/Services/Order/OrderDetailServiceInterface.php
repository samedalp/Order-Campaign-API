<?php

namespace App\Services\Order;

interface OrderDetailServiceInterface
{
    public function getOrderDetailByOrderNumber(string $orderNumber);
}
