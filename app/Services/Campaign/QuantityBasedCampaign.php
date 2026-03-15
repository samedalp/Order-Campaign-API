<?php

namespace App\Services\Campaign;

use App\DTOs\CampaignResult;
use App\DTOs\OrderDraft;

class QuantityBasedCampaign implements CampaignInterface
{
    public function __construct(
        private readonly int $productId,
        private readonly int $buyQuantity,
        private readonly int $payQuantity,
        private readonly int $maxFreeQuantity = 1
    ) {
    }

    public function code(): string
    {
        return 'QUANTITY_BASED';
    }

    public function name(): string
    {
        return 'Adet bazlı kampanya';
    }

    public function type(): string
    {
        return 'quantity_based';
    }

    public function checkCampaign(OrderDraft $draft): bool
    {
        foreach ($draft->items as $item) {

            if (
                (int) $item['product']->id === $this->productId &&
                (int) $item['quantity'] >= $this->buyQuantity
            ) {
                return true;
            }
        }

        return false;
    }

    public function calculate(OrderDraft $draft): CampaignResult
    {
        $discount = 0.0;

        foreach ($draft->items as $item) {

            if ((int) $item['product']->id === $this->productId) {

                $freeItems = floor($item['quantity'] / $this->buyQuantity)
                    * ($this->buyQuantity - $this->payQuantity);

                $freeItems = min($freeItems, $this->maxFreeQuantity);

                $discount = $freeItems * (float) $item['product']->price;
            }
        }

        $discount = round($discount, 2);

        return new CampaignResult(
            code: $this->code(),
            name: $this->name(),
            type: $this->type(),
            discountAmount: $discount,
            meta: [
                'product_id' => $this->productId,
                'buy_quantity' => $this->buyQuantity,
                'pay_quantity' => $this->payQuantity,
                'max_free_quantity' => $this->maxFreeQuantity
            ]
        );
    }
}
