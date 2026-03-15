<?php

namespace App\Services\Order;

use App\Exceptions\OrderNotFoundException;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderDetailService implements OrderDetailServiceInterface
{
    /**
     * @throws \Exception
     */
    public function getOrderDetailByOrderNumber(string $orderNumber): Order
    {
        try {
            return Cache::remember(
                "order_detail:{$orderNumber}",
                now()->addMinutes(10),
                fn () => $this->findOrderOrFail($orderNumber)
            );
        } catch (\Exception $e) {
            if ($e instanceof OrderNotFoundException) {
                throw $e;
            }

            Log::error(
                "Error occurred while getting order detail for order number {$orderNumber}: " . $e->getMessage()
            );

            throw new \Exception('Error while getting order detail, try again later', 0, $e);
        }
    }

    /**
     * @throws OrderNotFoundException
     */
    private function findOrderOrFail(string $orderNumber): Order
    {
        $order = Order::query()
            ->with(['items', 'campaignApplication'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            throw new OrderNotFoundException('Order not found');
        }

        return $order;
    }
}
