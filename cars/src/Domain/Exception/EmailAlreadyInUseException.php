<?php

namespace App\Domain\Exception;

use Exception;

class EmailAlreadyInUseException extends Exception
{
    public function __construct(string $message = "Email already in use")
    {
        parent::__construct($message);
    }
}
