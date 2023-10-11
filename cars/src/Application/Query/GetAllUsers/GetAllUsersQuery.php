<?php

namespace App\Application\Query\GetAllUsers;

use App\Domain\Query\QueryInterface;

class GetAllUsersQuery implements QueryInterface
{
    public function __construct(public int $page)
    {
    }
}
