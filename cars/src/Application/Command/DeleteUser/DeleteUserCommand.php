<?php

namespace App\Application\Command\DeleteUser;

use App\Domain\Command\CommandInterface;

class DeleteUserCommand implements CommandInterface
{
    public function __construct(public string $email)
    {
    }
}
