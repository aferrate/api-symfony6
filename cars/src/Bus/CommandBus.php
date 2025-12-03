<?php

namespace App\Bus;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Domain\Command\CommandBusInterface;
use App\Domain\Command\CommandInterface;

class CommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function execute(CommandInterface $command): mixed
    {
        return $this->handle($command);
    }
}
