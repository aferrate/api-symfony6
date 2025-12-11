<?php

namespace App\Domain\Event;

class CarCreatedEvent
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
