<?php

namespace App\Application\Query\GetAllCarsEnabled;

use App\Domain\Query\QueryInterface;

class GetAllCarsEnabledQuery implements QueryInterface
{
    public function __construct(public int $page)
    {
    }
}
