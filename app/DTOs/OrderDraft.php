<?php

namespace App\DTOs;

class OrderDraft
{
    public function __construct(
        public array $items,
        public float $subtotal,
    ) {
    }
}
