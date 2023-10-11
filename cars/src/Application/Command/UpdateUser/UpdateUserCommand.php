<?php

namespace App\Application\Command\UpdateUser;

use App\Domain\Command\CommandInterface;

class UpdateUserCommand implements CommandInterface
{
    public function __construct(public string $email, public array $params)
    {
    }
}
