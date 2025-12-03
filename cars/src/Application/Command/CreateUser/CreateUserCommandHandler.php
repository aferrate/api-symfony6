<?php

namespace App\Application\Command\CreateUser;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\UserRepoFactoryInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
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

    public function __invoke(CreateUserCommand $createUserCommand): array
    {
        if(!is_null($this->userReadRepo->findOneByEmail($createUserCommand->user->getEmail()))) {
            return ['error' => true, 'status' => 'failed', 'message' => 'email already registered'];
        }

        $this->userWriteRepo->save($createUserCommand->user);

        if(get_class($this->userWriteRepo) !== get_class($this->userReadRepo)) {
            $this->userReadRepo->save($createUserCommand->user);
        }

        $this->cacheClient->putIndex(
            $createUserCommand->user->toArray(), 'user_'.$createUserCommand->user->getEmail()
        );

        return ['error' => false, 'status' => 'user created!', 'id' => $createUserCommand->user->getId()];
    }
}
