<?php

namespace App\Application\Query\GetAllCars;

use App\Domain\Query\QueryInterface;

class GetAllCarsQuery implements QueryInterface
{
    public function __construct(public int $page)
    {
    }
}
