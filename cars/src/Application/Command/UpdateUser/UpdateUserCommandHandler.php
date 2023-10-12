<?php

namespace App\Application\Command\UpdateUser;

use App\Domain\Cache\CacheInterface;
use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\UserRepoFactoryInterface;

class UpdateUserCommandHandler implements CommandHandlerInterface
{
    private $userReadRepo;
    private $userWriteRepo;
    private $cacheClient;

    public function __construct(UserRepoFactoryInterface $userRepoFactory, CacheInterface $cacheClient)
    {
        $this->userReadRepo = $userRepoFactory->getUserReadRepo();
        $this->userWriteRepo = $userRepoFactory->getUserWriteRepo();
        $this->cacheClient = $cacheClient;
    }

    public function __invoke(UpdateUserCommand $updateUserCommand): array
    {
        $user = $this->userReadRepo->findOneByEmail($updateUserCommand->email);

        if(is_null($user)) {
            return ['error' => true, 'status' => 'no user found!'];
        }

        if (!is_null($this->userReadRepo->checkEmailRepeated($updateUserCommand->params['email'], $user->getId()))) {
            return ['error' => true, 'status' => 'email already in use!'];
        }

        $this->cacheClient->deleteIndex('user_'.$user->getEmail());

        $user->setEmail($updateUserCommand->params['email']);
        $user->setPassword($updateUserCommand->params['password']);

        $this->userWriteRepo->update($user);

        if(get_class($this->userWriteRepo) !== get_class($this->userReadRepo)) {
            $this->userReadRepo->update($user);
        }

        $this->cacheClient->putIndex($user->toArray(), 'user_'.$user->getEmail());

        return ['error' => false, 'status' => 'user updated!', 'id' => $user->getId()];
    }
}
