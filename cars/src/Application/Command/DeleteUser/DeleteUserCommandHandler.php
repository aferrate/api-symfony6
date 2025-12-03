<?php

namespace App\Application\Command\DeleteUser;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\UserRepoFactoryInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
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

    public function __invoke(DeleteUserCommand $deleteUserCommand): array
    {
        $user = $this->userReadRepo->findOneByEmail($deleteUserCommand->email);

        if(is_null($user)) {
            return ['error' => true, 'status' => 'no user found!'];
        }

        $this->userWriteRepo->delete($user);

        if(get_class($this->userWriteRepo) !== get_class($this->userReadRepo)) {
            $this->userReadRepo->delete($user);
        }

        $this->cacheClient->deleteIndex('user_'.$user->getEmail());

        return ['error' => false, 'status' => 'user deleted!', 'id' => $user->getId()];
    }
}
