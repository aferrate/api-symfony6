<?php

namespace App\Tests\Integration\Query;

use App\Application\Query\GetUserFromEmail\GetUserFromEmailQuery;
use App\Domain\Query\QueryBusInterface;
use App\Elasticsearch\Repository\UserRepository as UserRepoElastic;
use App\Doctrine\Repository\UserRepository;
use App\Entity\User;
use App\Services\CacheRedis;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetUserFromEmailTest extends KernelTestCase
{
    private $queryBus;
    private $userReadRepo;
    private $userWriteRepo;
    private $cache;

    public function setUp(): void
    {
        parent::setUp();
        $this->queryBus = $this::getContainer()->get(QueryBusInterface::class);
        $this->userReadRepo = $this::getContainer()->get(UserRepoElastic::class);
        $this->userWriteRepo = $this::getContainer()->get(UserRepository::class);
        $this->cache = $this::getContainer()->get(CacheRedis::class);
    }

    public function testGetUserFromEmail(): void
    {
        $user = new User(Uuid::uuid4());
        $user->setEmail('testIntegration@test.com');
        $user->setPassword('testIntegration');

        $this->userWriteRepo->save($user);
        $this->userReadRepo->save($user);
        $this->cache->putIndex($user->toArray(), 'user_'.$user->getEmail());

        sleep(10);

        $result = $this->queryBus->execute(new GetUserFromEmailQuery($user->getEmail()));

        $this->userReadRepo->delete($user);
        $this->cache->deleteIndex('user_'.$user->getEmail());

        $this->assertSame(['error' => false, 'data' => $user->toArray()], $result);
    }
}