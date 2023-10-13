<?php

namespace App\Tests\Integration\Command;

use App\Application\Command\CreateUser\CreateUserCommand;
use App\Domain\Command\CommandBusInterface;
use App\Factory\UserRepoFactory;
use App\Entity\User;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Services\CacheRedis;

class CreateUserTest extends KernelTestCase
{
    private $commandBus;
    private $userReadRepo;
    private $cache;

    public function setUp(): void
    {
        parent::setUp();
        $this->commandBus = $this::getContainer()->get(CommandBusInterface::class);
        $this->userReadRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserReadRepo();
        $this->cache = $this::getContainer()->get(CacheRedis::class);
    }

    public function testCreateUser(): void
    {
        $user = new User(Uuid::uuid4());
        $user->setEmail('testIntegration@test.com');
        $user->setPassword('testIntegration');

        $result = $this->commandBus->execute(new CreateUserCommand($user));

        $this->userReadRepo->delete($user);
        $this->cache->deleteIndex('user_'.$user->getEmail());

        $this->assertSame(['error' => false, 'status' => 'user created!', 'id' => $user->getId()], $result);
    }
}
