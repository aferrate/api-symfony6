<?php

namespace App\Infrastructure\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Domain\Event\DomainEventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyEventDispatcher implements DomainEventDispatcherInterface
{
    private EventDispatcherInterface $eventDispatcher;
    public MessageBusInterface $bus;

    public function __construct(EventDispatcherInterface $eventDispatcher, MessageBusInterface $bus)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->bus = $bus;
    }

    public function dispatch($event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function dispatchMessage($action): void
    {
        $this->bus->dispatch($event);
    }
}
