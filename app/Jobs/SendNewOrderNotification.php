<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNewOrderNotification implements ShouldQueue
{
    use Queueable;

    public int $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::with('items')->find($this->orderId);

        if (!$order) {
            Log::warning("Order notification job failed: order not found {$this->orderId}");
            return;
        }

        Log::info('New order notification job worked', [
            'order_number' => $order->order_number,
            'item_count' => $order->item_count,
            'grand_total' => $order->grand_total,
        ]);
    }
}
