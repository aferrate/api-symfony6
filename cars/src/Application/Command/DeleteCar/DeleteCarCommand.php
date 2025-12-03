<?php

namespace App\Application\Command\DeleteCar;

use App\Domain\Command\CommandInterface;

class DeleteCarCommand implements CommandInterface
{
    public function __construct(public string $id)
    {
    }
}
