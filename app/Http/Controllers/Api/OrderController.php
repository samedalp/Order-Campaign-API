<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\CreateOrderService;
use App\Services\Order\CreateOrderServiceInterface;
use App\Services\Order\OrderDetailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function __construct(
        private readonly CreateOrderServiceInterface $createOrderService,
        private readonly OrderDetailService          $orderDetailService
    )
    {
    }

    /**
     * @throws \Throwable
     */
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->createOrderService->createOrderWithCampaign($request->validated());
        return response()->json([
            'message' => 'Order created successfully',
            'data' => new OrderResource($order),
        ], 201);
    }

    /**
     * @throws \Exception
     */
    public function getOrderDetail(string $orderNumber): JsonResponse
    {
        $order = $this->orderDetailService->getOrderDetailByOrderNumber($orderNumber);
        return response()->json([
            'message' => 'Order fetched successfully',
            'data' => new OrderDetailResource($order),
        ]);
    }
}
