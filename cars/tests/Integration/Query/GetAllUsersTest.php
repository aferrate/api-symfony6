<?php

namespace App\Tests\Integration\Query;

use App\Application\Query\GetAllUsers\GetAllUsersQuery;
use App\Domain\Query\QueryBusInterface;
use App\Factory\UserRepoFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetAllUsersTest extends KernelTestCase
{
    private $queryBus;
    private $userReadRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->queryBus = $this::getContainer()->get(QueryBusInterface::class);
        $this->userReadRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserReadRepo();
    }

    public function testGetAllUsers(): void
    {
        $result = $this->queryBus->execute(new GetAllUsersQuery(0));

        $users = $this->userReadRepo->findAllUsers(0);

        $usersArray = [];

        foreach ($users as $user) {
            $usersArray[] = $user->toArray();
        }

        $this->assertSame($usersArray, $result);
    }
}
