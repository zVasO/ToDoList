<?php

namespace App\Exception;

use Exception;

class AccessException extends Exception
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
