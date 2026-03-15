<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'status' => $this->status,
            'currency' => $this->currency,
            'item_count' => $this->item_count,
            'totals' => [
                'subtotal' => $this->subtotal,
                'discount_total' => $this->discount_total,
                'shipping_total' => $this->shipping_total,
                'grand_total' => $this->grand_total,
            ],
            'campaign' => $this->campaignApplication ? [
                'code' => $this->campaignApplication->campaign_code,
                'name' => $this->campaignApplication->campaign_name,
                'type' => $this->campaignApplication->campaign_type,
                'discount_amount' => $this->campaignApplication->discount_amount,
                'meta' => $this->campaignApplication->meta,
            ] : null,
            'items' => $this->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'author_name' => $item->author_name,
                    'category_name' => $item->category_name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'line_subtotal' => $item->line_subtotal,
                    'line_discount' => $item->line_discount,
                    'line_total' => $item->line_total,
                ];
            })->values(),
            'created_at' => $this->created_at,
        ];
    }
}
