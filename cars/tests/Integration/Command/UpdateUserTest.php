<?php

namespace App\Tests\Integration\Command;

use App\Application\Command\UpdateUser\UpdateUserCommand;
use App\Domain\Command\CommandBusInterface;
use App\Entity\User;
use App\Factory\UserRepoFactory;
use App\Services\CacheRedis;
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

    public function testUpdateCar(): void
    {
        $user = new User(Uuid::uuid4());
        $user->setEmail('testIntegration@test.com');
        $user->setPassword('testIntegration');
        $arrayUser = ['email' => 'testIntegration2@test.com', 'password' => 'testIntegration2'];

        $this->userWriteRepo->save($user);
        $this->userReadRepo->save($user);
        $this->cache->putIndex($user->toArray(), 'user_'.$user->getEmail());

        sleep(10);

        $result = $this->commandBus->execute(new UpdateUserCommand('testIntegration@test.com', $arrayUser));

        $this->userReadRepo->delete($user);
        $this->cache->deleteIndex('user_'.$user->getEmail());

        $this->assertSame(['error' => false, 'status' => 'user updated!', 'id' => $user->getId()], $result);
    }
}
