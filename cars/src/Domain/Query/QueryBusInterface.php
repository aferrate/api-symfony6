<?php

namespace App\Domain\Query;

interface QueryBusInterface
{
    public function execute(QueryInterface $query): mixed;
}
