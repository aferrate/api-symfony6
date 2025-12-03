<?php

namespace App\Application\Command\UpdateUser;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\UserRepoFactoryInterface;
use App\Domain\Exception\UserNotFoundException;
use App\Domain\Exception\EmailAlreadyInUseException;

class UpdateUserCommandHandler implements CommandHandlerInterface
{
    private $userReadRepo;
    private $userWriteRepo;
    private $cacheClient;

    public function __construct(UserRepoFactoryInterface $userRepoFactory, CacheFactoryInterface $cacheFactory)
    {
        $this->userReadRepo = $userRepoFactory->getUserReadRepo();
        $this->userWriteRepo = $userRepoFactory->getUserWriteRepo();
        $this->cacheClient = $cacheFactory->getCache();
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

        $this->cacheClient->deleteIndex('user_'.$user->getEmail());

        $user->setEmail($updateUserCommand->user->getEmail());
        $user->setPassword($updateUserCommand->user->getPassword());

        $this->userWriteRepo->update($user);

        if(get_class($this->userWriteRepo) !== get_class($this->userReadRepo)) {
            $updateUserCommand->user->setId($user->getId());

            $this->userReadRepo->update($updateUserCommand->user);
        }

        $this->cacheClient->putIndex($user->toArray(), 'user_'.$user->getEmail());

        return ['error' => false, 'status' => 'user updated!', 'id' => $user->getId()];
    }
}
