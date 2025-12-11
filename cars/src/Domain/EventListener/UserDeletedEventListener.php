<?php

namespace App\Domain\EventListener;

use App\Domain\Event\UserDeletedEvent;
use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Message\EmailMessageInterface;

class UserDeletedEventListener
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

    public function onUserDeleted(UserDeletedEvent $event)
    {
        try {
            $this->cacheClient->deleteIndex('user_' . $event->getUserId());

            $this->sendEmailMessage->setMsg('User deleted with id ' . $event->getUserId());
            $this->sendEmailMessage->setSubject('User deleted');

            $this->eventDispatcher->bus->dispatch($this->sendEmailMessage);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
