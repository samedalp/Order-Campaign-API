<?php

namespace App\Services\Campaign;

use App\DTOs\CampaignResult;
use App\DTOs\OrderDraft;
interface CampaignInterface
{
    public function code(): string;

    public function name(): string;

    public function type(): string;

    public function checkCampaign(OrderDraft $draft): bool;

    public function calculate(OrderDraft $draft): CampaignResult;
}
