<?php

namespace App\Helpers;

use App\Exceptions\OrderNotFoundException;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\StockException;

trait ErrorHandlerHelper
{
    public const PASSTHROUGH_EXCEPTIONS = [
        ProductNotFoundException::class,
        StockException::class,
        OrderNotFoundException::class,
    ];

    public function isDefinedException(\Throwable $exception): bool
    {
        foreach (self::PASSTHROUGH_EXCEPTIONS as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }
}
