<?php

namespace App\Tests\Integration\Command;

use App\Application\Command\DeleteCar\DeleteCarCommand;
use App\Domain\Command\CommandBusInterface;
use App\Entity\Car;
use App\Factory\CarRepoFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Services\CacheRedis;
use DateTime;

class DeleteCarTest extends KernelTestCase
{
    private $commandBus;
    private $carWriteRepo;
    private $carReadRepo;
    private $cache;

    public function setUp(): void
    {
        parent::setUp();
        $this->commandBus = $this::getContainer()->get(CommandBusInterface::class);
        $this->carWriteRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarWriteRepo();
        $this->carReadRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarReadRepo();
        $this->cache = $this::getContainer()->get(CacheRedis::class);
    }

    public function testDeleteCar(): void
    {
        $car = new Car(Uuid::uuid4());
        $car->setYear(2000);
        $car->setCity('testIntegration');
        $car->setCountry('testIntegration');
        $car->setDescription('testIntegration');
        $car->setEnabled(true);
        $car->setImageFilename('testIntegration.jpg');
        $car->setMark('testIntegration');
        $car->setModel('testIntegration');
        $car->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
        $car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));

        $this->carWriteRepo->save($car);
        $this->carReadRepo->save($car);
        $this->cache->putIndex($car->toArray(), 'car_'.$car->getId());

        sleep(10);

        $result = $this->commandBus->execute(new DeleteCarCommand($car->getId()));

        $this->assertSame(['error' => false, 'status' => 'car deleted!', 'id' => $car->getId()], $result);
    }
}
