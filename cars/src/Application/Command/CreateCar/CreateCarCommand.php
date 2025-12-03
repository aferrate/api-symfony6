<?php

namespace App\Application\Command\CreateCar;

use App\Domain\Command\CommandInterface;
use App\Domain\Model\Car;

class CreateCarCommand implements CommandInterface
{
    public function __construct(public Car $car)
    {
    }
}
