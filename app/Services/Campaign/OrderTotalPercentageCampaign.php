<?php

namespace App\Services\Campaign;

use App\DTOs\CampaignResult;
use App\DTOs\OrderDraft;

class OrderTotalPercentageCampaign implements CampaignInterface
{
    public function __construct(
        private readonly float $minTotal,
        private readonly float $rate
    ) {
    }

    public function code(): string
    {
        return 'ORDER_TOTAL_PERCENT';
    }

    public function name(): string
    {
        return 'Sipariş toplamına yüzde indirim';
    }

    public function type(): string
    {
        return 'order_total_percentage';
    }

    public function checkCampaign(OrderDraft $draft): bool
    {
        return $draft->subtotal >= $this->minTotal;
    }

    public function calculate(OrderDraft $draft): CampaignResult
    {
        $discount = round($draft->subtotal * ($this->rate / 100), 2);

        return new CampaignResult(
            code: $this->code(),
            name: $this->name(),
            type: $this->type(),
            discountAmount: $discount,
            meta: [
                'min_total' => $this->minTotal,
                'rate' => $this->rate,
            ]
        );
    }
}
