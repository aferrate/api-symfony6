<?php

namespace App\Application\Command\CreateUser;

use App\Domain\Command\CommandInterface;
use App\Domain\Model\User;

class CreateUserCommand implements CommandInterface
{
    public function __construct(public User $user)
    {
    }
}
