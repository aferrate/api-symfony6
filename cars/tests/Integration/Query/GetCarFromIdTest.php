<?php

namespace App\Tests\Integration\Query;

use App\Application\Query\GetCarFromId\GetCarFromIdQuery;
use App\Domain\Query\QueryBusInterface;
use App\Entity\Car;
use App\Factory\CarRepoFactory;
use App\Services\CacheRedis;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTime;

class GetCarFromIdTest extends KernelTestCase
{
    private $queryBus;
    private $carReadRepo;
    private $carWriteRepo;
    private $cache;

    public function setUp(): void
    {
        parent::setUp();
        $this->queryBus = $this::getContainer()->get(QueryBusInterface::class);
        $this->carReadRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarReadRepo();
        $this->carWriteRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarWriteRepo();
        $this->cache = $this::getContainer()->get(CacheRedis::class);
    }

    public function testGetCarFromId(): void
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

        $result = $this->queryBus->execute(new GetCarFromIdQuery($car->getId()));

        $this->carReadRepo->delete($car);
        $this->cache->deleteIndex('car_'.$car->getId());

        $this->assertSame(['error' => false, 'data' => $car->toArray()], $result);
    }
}
