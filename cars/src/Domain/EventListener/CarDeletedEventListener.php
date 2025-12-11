<?php

namespace App\Domain\EventListener;

use App\Domain\Event\CarDeletedEvent;
use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Message\EmailMessageInterface;

class CarDeletedEventListener
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

    public function onCarDeleted(CarDeletedEvent $event)
    {
        try {
            $this->cacheClient->deleteIndex('car_'.$event->getCarId());

            $this->sendEmailMessage->setMsg('Car deleted with id '. $event->getCarId());
            $this->sendEmailMessage->setSubject('Car deleted');

            $this->eventDispatcher->bus->dispatch($this->sendEmailMessage);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
