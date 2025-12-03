<?php

namespace App\Tests\Integration\Command;

use App\Application\Command\DeleteUser\DeleteUserCommand;
use App\Domain\Command\CommandBusInterface;
use App\Factory\UserRepoFactory;
use App\Entity\User;
use App\Services\CacheRedis;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteUserTest extends KernelTestCase
{
    private $commandBus;
    private $userWriteRepo;
    private $userReadRepo;
    private $cache;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->commandBus = $this::getContainer()->get(CommandBusInterface::class);
        $this->userWriteRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserWriteRepo();
        $this->userReadRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserReadRepo();
        $this->cache = $this::getContainer()->get(CacheRedis::class);

        $this->user = new User(Uuid::uuid4());
        $this->user->setEmail('testIntegration@test.com');
        $this->user->setPassword('testIntegration');

        $this->userWriteRepo->save($this->user);
        $this->userReadRepo->save($this->user);
        $this->cache->putIndex($this->user->toArray(), 'user_'.$this->user->getEmail());
    }

    public function testDeleteUser(): void
    {
        sleep(10);

        $result = $this->commandBus->execute(new DeleteUserCommand($this->user->getEmail()));

        $this->assertSame(['error' => false, 'status' => 'user deleted!', 'id' => $this->user->getId()], $result);
    }
}
