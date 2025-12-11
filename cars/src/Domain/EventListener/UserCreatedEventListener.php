<?php

namespace App\Domain\EventListener;

use App\Domain\Event\UserCreatedEvent;
use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Message\EmailMessageInterface;

class UserCreatedEventListener
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

    public function onUserCreated(UserCreatedEvent $event)
    {
        try {
            $this->cacheClient->putIndex($event->getUser()->toArray(), 'user_' . $event->getUser()->getId());

            $this->sendEmailMessage->setMsg('User created with id '. $event->getUser()->getId());
            $this->sendEmailMessage->setSubject('User created');

            $this->eventDispatcher->bus->dispatch($this->sendEmailMessage);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
