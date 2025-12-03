<?php

namespace App\Application\Query\GetUserFromEmail;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Factory\UserRepoFactoryInterface;
use App\Domain\Query\QueryHandlerInterface;

class GetUserFromEmailQueryHandler implements QueryHandlerInterface
{
    private $userReadRepo;
    private $cacheClient;

    public function __construct(UserRepoFactoryInterface $userRepoFactory, CacheFactoryInterface $cacheFactory)
    {
        $this->userReadRepo = $userRepoFactory->getUserReadRepo();
        $this->cacheClient = $cacheFactory->getCache();
    }

    public function __invoke(GetUserFromEmailQuery $qetUserFromEmailQuery): array
    {
        $cacheUser = $this->cacheClient->getIndex('user_'.$qetUserFromEmailQuery->email);

        if($cacheUser) {
            return ['error' => false, 'data' => json_decode($cacheUser, true)];
        }

        $user = $this->userReadRepo->findOneByEmail($qetUserFromEmailQuery->email);

        if(is_null($user)) {
            return ['error' => true, 'status' => 'no user found!'];
        }

        $this->cacheClient->putIndex($user->toArray(), 'user_'.$user->getEmail());

        return ['error' => false, 'data' => $user->toArray()];
    }
}