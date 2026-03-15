<?php

namespace App\DTOs;

class CampaignResult
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $type,
        public readonly float $discountAmount,
        public readonly array $meta = [],
    ) {
    }
}
