<?php

namespace App\Application\Command\CreateUser;

use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\UserRepoFactoryInterface;
use App\Domain\Exception\EmailAlreadyInUseException;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Event\UserCreatedEvent;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    private $userReadRepo;
    private $userWriteRepo;
    private $eventDispatcher;

    public function __construct(UserRepoFactoryInterface $userRepoFactory, DomainEventDispatcherInterface $eventDispatcher)
    {
        $this->userReadRepo = $userRepoFactory->getUserReadRepo();
        $this->userWriteRepo = $userRepoFactory->getUserWriteRepo();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateUserCommand $createUserCommand): array
    {
        if(!is_null($this->userReadRepo->findOneByEmail($createUserCommand->user->getEmail()))) {
            throw new EmailAlreadyInUseException();
        }

        $this->userWriteRepo->save($createUserCommand->user);

        if(get_class($this->userWriteRepo) !== get_class($this->userReadRepo)) {
            $this->userReadRepo->save($createUserCommand->user);
        }

        $this->eventDispatcher->dispatch(new UserCreatedEvent($createUserCommand->user));

        return ['error' => false, 'status' => 'user created!', 'id' => $createUserCommand->user->getId()];
    }
}
