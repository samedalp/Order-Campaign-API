<?php

namespace App\Services\Campaign;

use App\DTOs\CampaignResult;
use App\DTOs\OrderDraft;

class CampaignService implements CampaignServiceInterface
{
    /**
     * @param CampaignInterface[] $campaigns
     */
    public function getBestCampaign(OrderDraft $draft, array $campaigns): ?CampaignResult
    {
        $results = [];

        foreach ($campaigns as $campaign) {
            if ($campaign->checkCampaign($draft)) {
                $results[] = $campaign->calculate($draft);
            }
        }

        if (empty($results)) {
            return null;
        }

        usort(
            $results,
            fn (CampaignResult $a, CampaignResult $b) => $b->discountAmount <=> $a->discountAmount
        );

        return $results[0];
    }
}
