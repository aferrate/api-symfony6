<?php

namespace App\Application\Command\UpdateCar;

use App\Domain\Command\CommandInterface;

class UpdateCarCommand implements CommandInterface
{
    public function __construct(public string $id, public array $params)
    {
    }
}
