<?php

namespace App\Exception;

use Exception;

class ProductOutOfStockException extends Exception
{
    public function __construct(string $message = '', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}