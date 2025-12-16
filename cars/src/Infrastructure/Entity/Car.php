<?php

namespace App\Infrastructure\Entity;

use App\Domain\Model\Car as DomainCar;

class Car extends DomainCar
{
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $idCar
     * @return Car
     */
    static public function createCar(string $idCar): Car
    {
        $car = new Car($idCar);

        return $car;
    }
}
