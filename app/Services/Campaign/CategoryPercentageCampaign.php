<?php

namespace App\Services\Campaign;

use App\DTOs\CampaignResult;
use App\DTOs\OrderDraft;

class CategoryPercentageCampaign implements CampaignInterface
{
    public function __construct(
        private readonly int $categoryId,
        private readonly float $rate
    ) {
    }

    public function code(): string
    {
        return 'CATEGORY_PERCENT';
    }

    public function name(): string
    {
        return 'Kategori bazlı yüzde indirim';
    }

    public function type(): string
    {
        return 'category_percentage';
    }

    public function checkCampaign(OrderDraft $draft): bool
    {
        foreach ($draft->items as $item) {
            if ((int) $item['product']->category_id === $this->categoryId) {
                return true;
            }
        }

        return false;
    }

    public function calculate(OrderDraft $draft): CampaignResult
    {
        $discountBase = 0.0;

        foreach ($draft->items as $item) {
            if ((int) $item['product']->category_id === $this->categoryId) {
                $discountBase += $item['line_subtotal'];
            }
        }

        $discount = round($discountBase * ($this->rate / 100), 2);

        return new CampaignResult(
            code: $this->code(),
            name: $this->name(),
            type: $this->type(),
            discountAmount: $discount,
            meta: [
                'category_id' => $this->categoryId,
                'rate' => $this->rate,
                'discount_base' => round($discountBase, 2),
            ]
        );
    }
}
