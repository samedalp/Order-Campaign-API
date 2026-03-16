<?php

namespace App\Services\Order;

use App\Helpers\ErrorHandlerHelper;
use App\DTOs\OrderDraft;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\StockException;
use App\Jobs\SendNewOrderNotification;
use App\Models\CampaignApplication;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Campaign\CampaignServiceInterface;
use App\Services\Campaign\CategoryPercentageCampaign;
use App\Services\Campaign\OrderTotalPercentageCampaign;
use App\Services\Campaign\QuantityBasedCampaign;
use App\Services\Shipping\ShippingCalculatorInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateOrderService implements CreateOrderServiceInterface
{

    use ErrorHandlerHelper;
    public function __construct(
        private readonly CampaignServiceInterface      $campaignService,
        private readonly ShippingCalculatorInterface   $shippingCalculator,
        private readonly OrderNumberGeneratorInterface $orderNumberGenerator,
    )
    {
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function createOrderWithCampaign(array $payload): Order
    {
        try {
            $requestedItems = collect($payload['items']);

            $productIds = $requestedItems
                ->pluck('product_id')
                ->unique()
                ->values()
                ->all();

            $products = Product::query()
                ->with(['category', 'author'])
                ->whereIn('id', $productIds)
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            $draftItems = [];
            $subtotal = 0.0;


            foreach ($requestedItems as $requestedItem) {
                $productId = (int)$requestedItem['product_id'];
                $quantity = (int)$requestedItem['quantity'];

                $product = $products->get($productId);

                if (!$product) {
                    throw new ProductNotFoundException("Product {$productId} not found");
                }

                if ($product->stock < $quantity) {
                    throw new StockException("Low stock for product {$product->id}");
                }

                $lineSubtotal = round((float)$product->price * $quantity, 2);
                $subtotal += $lineSubtotal;

                $draftItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_subtotal' => $lineSubtotal,
                ];
            }

            $draft = new OrderDraft(
                items: $draftItems,
                subtotal: round($subtotal, 2),
            );

            $campaigns = [
                new OrderTotalPercentageCampaign(100, 5),
                new CategoryPercentageCampaign(2, 10),
                new QuantityBasedCampaign(1, 2, 1, 1),
            ];

            $campaignResult = $this->campaignService->getBestCampaign($draft, $campaigns);

            $discountTotal = $campaignResult?->discountAmount ?? 0.0;
            $shippingTotal = $this->shippingCalculator->calculate($draft->subtotal);
            $grandTotal = round($draft->subtotal - $discountTotal + $shippingTotal, 2);

            $prepareOrder = [
                'order_number' => $this->orderNumberGenerator->generate(),
                'status' => 'created',
                'currency' => 'TRY',
                'item_count' => collect($draftItems)->sum('quantity'),
                'subtotal' => $draft->subtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shippingTotal,
                'grand_total' => $grandTotal,
                'campaign_code' => $campaignResult?->code,
                'campaign_name' => $campaignResult?->name,
                'campaign_type' => $campaignResult?->type,
            ];

            DB::beginTransaction();

            $order = Order::create($prepareOrder);

            if (!$order) {
                throw new \Exception('Error while creating order ' . json_encode($prepareOrder));
            }

            $this->createOrderItems($draftItems, $draft, $discountTotal, $order);

            if ($campaignResult) {
                CampaignApplication::create([
                    'order_id' => $order->id,
                    'campaign_code' => $campaignResult->code,
                    'campaign_name' => $campaignResult->name,
                    'campaign_type' => $campaignResult->type,
                    'discount_amount' => $campaignResult->discountAmount,
                    'meta' => $campaignResult->meta,
                ]);
            }

            DB::commit();
            SendNewOrderNotification::dispatch($order->id);

            return $order;
        } catch (\Exception $exception) {
            Log::error('Error while creating order: ' . $exception->getMessage());
            $this->handleOrderCreateError($exception);
        }
    }

    /**
     * @throws \Exception|\Throwable
     */
    private function createOrderItems(array $draftItems, OrderDraft $draft, float $discountTotal, Order $order): void
    {
        try {
            foreach ($draftItems as $draftItem) {
                /** @var Product $product */
                $product = $draftItem['product'];
                $quantity = $draftItem['quantity'];
                $lineSubtotal = $draftItem['line_subtotal'];

                $lineDiscount = 0.0;

                if ($draft->subtotal > 0 && $discountTotal > 0) {
                    $discountRatio = $lineSubtotal / $draft->subtotal;
                    $lineDiscount = round($discountTotal * $discountRatio, 2);
                }

                $prepareOrderItem = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'author_name' => $product->author?->name,
                    'category_name' => $product->category?->name,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'line_subtotal' => $lineSubtotal,
                    'line_discount' => $lineDiscount,
                    'line_total' => round($lineSubtotal - $lineDiscount, 2),
                ];

                $result = OrderItem::create($prepareOrderItem);

                if (!$result) {
                    throw new \Exception('Error while creating order items ' . json_encode($prepareOrderItem));
                }

                $this->updateProductStock($product, $quantity);
            }
        } catch (\Exception $exception) {
            Log::error("Error while creating order {$order->id} items: " . $exception->getMessage());
            $this->handleOrderCreateError($exception);
        }
    }

    /**
     * @throws \Exception|\Throwable
     */
    private function updateProductStock(Product $product, int $quantity): void
    {
        try {
            $updatedRows = Product::query()
                ->where('id', $product->id)
                ->where('stock', '>=', $quantity)
                ->decrement('stock', $quantity);

            if (!$updatedRows) {
                throw new \Exception("Stock update failed for product {$product->id}");
            }
        } catch (\Exception $exception) {
            Log::error("Error while updating product {$product->id} stock: " . $exception->getMessage());
            $this->handleOrderCreateError($exception);
        }
    }

    /**
     * @throws \Exception|\Throwable
     */
    private function handleOrderCreateError(\Throwable $exception)
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        if ($this->isDefinedException($exception)) {
            throw $exception;
        }

        throw new \Exception(
            'Error while creating order, please try again later',
            0,
            $exception
        );
    }



}
