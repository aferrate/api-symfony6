<?php

namespace App\Application\Command\UpdateUser;

use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\UserRepoFactoryInterface;
use App\Domain\Exception\UserNotFoundException;
use App\Domain\Exception\EmailAlreadyInUseException;
use App\Domain\Event\UserUpdatedEvent;
use App\Domain\Event\DomainEventDispatcherInterface;

class UpdateUserCommandHandler implements CommandHandlerInterface
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

    public function __invoke(UpdateUserCommand $updateUserCommand): array
    {
        $user = $this->userReadRepo->findOneByEmail($updateUserCommand->email);
        
        if(is_null($user)) {
            throw new UserNotFoundException();
        }
        
        if (!is_null($this->userReadRepo->checkEmailRepeated($updateUserCommand->user->getEmail(), $user->getId()))) {
            throw new EmailAlreadyInUseException();
        }

        $user->setEmail($updateUserCommand->user->getEmail());
        $user->setPassword($updateUserCommand->user->getPassword());

        $this->userWriteRepo->update($user);

        if(get_class($this->userWriteRepo) !== get_class($this->userReadRepo)) {
            $updateUserCommand->user->setId($user->getId());

            $this->userReadRepo->update($updateUserCommand->user);
        }

        $this->eventDispatcher->dispatch(new UserUpdatedEvent($user));

        return ['error' => false, 'status' => 'user updated!', 'id' => $user->getId()];
    }
}
