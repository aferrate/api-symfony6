<?php

namespace App\Tests\Integration\Command;

use App\Application\Command\CreateCar\CreateCarCommand;
use App\Domain\Command\CommandBusInterface;
use App\Factory\CarRepoFactory;
use App\Services\CacheRedis;
use App\Entity\Car;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCarTest extends KernelTestCase
{
    private $commandBus;
    private $carReadRepo;
    private $cache;

    public function setUp(): void
    {
        parent::setUp();
        $this->commandBus = $this::getContainer()->get(CommandBusInterface::class);
        $this->carReadRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarReadRepo();
        $this->cache = $this::getContainer()->get(CacheRedis::class);
    }

    public function testCreateCar(): void
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

        $result = $this->commandBus->execute(new CreateCarCommand($car));

        $this->carReadRepo->delete($car);
        $this->cache->deleteIndex('car_'.$car->getId());

        $this->assertSame(['error' => false, 'status' => 'car created!', 'id' => $car->getId()], $result);
    }
}
