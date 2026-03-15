<?php

namespace App\Exceptions;

use Exception;

class StockException extends Exception
{
    public function __construct(string $message = "Not enough stock", int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
