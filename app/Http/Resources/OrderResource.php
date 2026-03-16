<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'subtotal' => $this->subtotal,
            'discount_total' => $this->discount_total,
            'shipping_total' => $this->shipping_total,
            'grand_total' => $this->grand_total,
            'used_campaign' => $this->campaignApplication ? [
                'code' => $this->campaignApplication->campaign_code,
                'name' => $this->campaignApplication->campaign_name,
                'type' => $this->campaignApplication->campaign_type,
                'discount_amount' => $this->campaignApplication->discount_amount,
                'meta' => $this->campaignApplication->meta,
            ] : null,
        ];
    }
}
