<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'status',
        'currency',
        'item_count',
        'subtotal',
        'discount_total',
        'shipping_total',
        'grand_total',
        'campaign_code',
        'campaign_name',
        'campaign_type',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function campaignApplication(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CampaignApplication::class);
    }
}
