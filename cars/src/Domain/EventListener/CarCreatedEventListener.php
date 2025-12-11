<?php

namespace App\Domain\EventListener;

use App\Domain\Event\CarCreatedEvent;
use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Message\EmailMessageInterface;

class CarCreatedEventListener
{
    private $cacheClient;
    private $eventDispatcher;
    private $sendEmailMessage;

    public function __construct(CacheFactoryInterface $cacheFactory, DomainEventDispatcherInterface $eventDispatcher, EmailMessageInterface $sendEmailMessage)
    {
        $this->cacheClient = $cacheFactory->getCache();
        $this->sendEmailMessage = $sendEmailMessage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onCarCreated(CarCreatedEvent $event)
    {
        try {
            $this->cacheClient->putIndex($event->getCar()->toArray(), 'car_' . $event->getCar()->getId());

            $this->sendEmailMessage->setMsg('Car created with id '. $event->getCar()->getId());
            $this->sendEmailMessage->setSubject('Car created');

            $this->eventDispatcher->bus->dispatch($this->sendEmailMessage);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
