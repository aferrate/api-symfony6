<?php

namespace App\Application\Query\GetAllUsers;

use App\Domain\Factory\UserRepoFactoryInterface;
use App\Domain\Query\QueryHandlerInterface;

class GetAllUsersQueryHandler implements QueryHandlerInterface
{
    private $userReadRepo;

    public function __construct(UserRepoFactoryInterface $userRepoFactory)
    {
        $this->userReadRepo = $userRepoFactory->getUserReadRepo();
    }

    public function __invoke(GetAllUsersQuery $getAllUsersQuery): array
    {
        $users = $this->userReadRepo->findAllUsers($getAllUsersQuery->page);
        $usersArray = [];

        foreach ($users as $user) {
            $usersArray[] = $user->toArray();
        }

        return $usersArray;
    }
}
