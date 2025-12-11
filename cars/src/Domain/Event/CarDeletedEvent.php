<?php

namespace App\Domain\Event;

class CarDeletedEvent
{
    private $carId;

    public function __construct(string $carId)
    {
        $this->carId = $carId;
    }

    public function getCarId()
    {
        return $this->carId;
    }
}
