<?php

namespace App\Helpers;

use App\Services\Campaign\CategoryPercentageCampaign;
use App\Services\Campaign\OrderTotalPercentageCampaign;
use App\Services\Campaign\QuantityBasedCampaign;

class CampaignPresenters
{
    public static function getCampaigns(): array
    {
        return [
            new OrderTotalPercentageCampaign(100, 5),
            new CategoryPercentageCampaign(2, 10),
            new QuantityBasedCampaign(1, 2, 1, 1),
        ];
    }
}
