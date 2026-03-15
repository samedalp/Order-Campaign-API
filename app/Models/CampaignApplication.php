<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignApplication extends Model
{
    protected $fillable = [
        'order_id',
        'campaign_code',
        'campaign_name',
        'campaign_type',
        'discount_amount',
        'meta',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'meta' => 'array',
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
