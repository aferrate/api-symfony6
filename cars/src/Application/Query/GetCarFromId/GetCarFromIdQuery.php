<?php

namespace App\Application\Query\GetCarFromId;

use App\Domain\Query\QueryInterface;

class GetCarFromIdQuery implements QueryInterface
{
    public function __construct(public string $id)
    {
    }
}
