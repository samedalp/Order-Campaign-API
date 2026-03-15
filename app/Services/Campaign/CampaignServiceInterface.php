<?php

namespace App\Services\Campaign;

use App\DTOs\CampaignResult;
use App\DTOs\OrderDraft;

interface CampaignServiceInterface
{
    /**
     * @param CampaignInterface[] $campaigns
     */
    public function getBestCampaign(OrderDraft $draft, array $campaigns): ?CampaignResult;
}
