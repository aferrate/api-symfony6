<?php

namespace App\Bus;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Domain\Query\QueryBusInterface;
use App\Domain\Query\QueryInterface;

class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function execute(QueryInterface $query): mixed
    {
        return $this->handle($query);
    }
}
