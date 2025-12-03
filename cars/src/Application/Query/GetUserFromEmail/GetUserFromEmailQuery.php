<?php

namespace App\Application\Query\GetUserFromEmail;

use App\Domain\Query\QueryInterface;

class GetUserFromEmailQuery implements QueryInterface
{
    public function __construct(public string $email)
    {
    }
}