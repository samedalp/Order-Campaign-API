<?php

namespace App\Exceptions;

use Exception;

class OrderNotFoundException extends Exception
{
    public function __construct(string $message = "Order detail not found", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
