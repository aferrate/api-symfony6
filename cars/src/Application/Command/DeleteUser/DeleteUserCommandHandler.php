<?php

namespace App\Application\Command\DeleteUser;

use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\UserRepoFactoryInterface;
use App\Domain\Exception\UserNotFoundException;
use App\Domain\Event\UserDeletedEvent;
use App\Domain\Event\DomainEventDispatcherInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
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

    public function __invoke(DeleteUserCommand $deleteUserCommand): array
    {
        $user = $this->userReadRepo->findOneByEmail($deleteUserCommand->email);

        if(is_null($user)) {
            throw new UserNotFoundException();
        }

        $this->userWriteRepo->delete($user);

        if(get_class($this->userWriteRepo) !== get_class($this->userReadRepo)) {
            $this->userReadRepo->delete($user);
        }

        $this->eventDispatcher->dispatch(new UserDeletedEvent($user->getId()));

        return ['error' => false, 'status' => 'user deleted!', 'id' => $user->getId()];
    }
}
