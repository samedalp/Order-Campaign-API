<?php

namespace App\Const;

class ErrorConstants
{
    public const PASSTHROUGH_EXCEPTIONS = [
        \App\Exceptions\ProductNotFoundException::class,
        \App\Exceptions\StockException::class,
    ];
}
