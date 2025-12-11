<?php

namespace App\Domain\Event;

class CarUpdatedEvent
{
    private $car;

    public function __construct($car)
    {
        $this->car = $car;
    }

    public function getCar()
    {
        return $this->car;
    }
}
