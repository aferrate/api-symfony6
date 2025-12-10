<?php

namespace App\Tests\Integration\Command;

use App\Application\Command\UpdateUser\UpdateUserCommand;
use App\Domain\Command\CommandBusInterface;
use App\Infrastructure\Entity\User;
use App\Infrastructure\Factory\UserRepoFactory;
use App\Infrastructure\Services\CacheRedis;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateUserTest extends KernelTestCase
{
    private $commandBus;
    private $userWriteRepo;
    private $userReadRepo;
    private $cache;

    public function setUp(): void
    {
        parent::setUp();
        $this->commandBus = $this::getContainer()->get(CommandBusInterface::class);
        $this->userWriteRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserWriteRepo();
        $this->userReadRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserReadRepo();
        $this->cache = $this::getContainer()->get(CacheRedis::class);
    }

    public function testUpdateUser(): void
    {
        $user = new User(Uuid::uuid4());
        $user->setEmail('testIntegration@test.com');
        $user->setPassword('testIntegration');

        $this->userWriteRepo->save($user);
        $this->userReadRepo->save($user);
        $this->cache->putIndex($user->toArray(), 'user_'.$user->getEmail());

        sleep(10);

        $user->setEmail('testIntegration222@test.com');
        $user->setPassword('testIntegration2222222');

        $result = $this->commandBus->execute(new UpdateUserCommand($user ,'testIntegration@test.com'));

        $this->userReadRepo->delete($user);
        $this->cache->deleteIndex('user_'.$user->getEmail());

        $this->assertSame(['error' => false, 'status' => 'user updated!', 'id' => $user->getId()], $result);
    }
}
