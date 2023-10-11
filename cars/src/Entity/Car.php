<?php

namespace App\Entity;

use App\Domain\Model\Car as DomainCar;
use Ramsey\Uuid\Uuid;

class Car extends DomainCar
{
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return Car
     */
    static public function createCar(): Car
    {
        $car = new Car(Uuid::uuid4());

        return $car;
    }
}
