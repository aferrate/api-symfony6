<?php

namespace App\Application\Command\UpdateUser;

use App\Domain\Command\CommandInterface;
use App\Domain\Model\User;

class UpdateUserCommand implements CommandInterface
{
    public function __construct(public User $user, public string $email)
    {
    }
}
