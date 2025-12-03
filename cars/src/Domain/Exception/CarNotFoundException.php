<?php

namespace App\Domain\Exception;

use Exception;

class CarNotFoundException extends Exception
{
    public function __construct(string $message = "Car not found")
    {
        parent::__construct($message);
    }
}
