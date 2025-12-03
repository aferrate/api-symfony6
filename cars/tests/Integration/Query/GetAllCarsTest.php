<?php

namespace App\Tests\Integration\Query;

use App\Application\Query\GetAllCars\GetAllCarsQuery;
use App\Domain\Query\QueryBusInterface;
use App\Factory\CarRepoFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetAllCarsTest extends KernelTestCase
{
    private $queryBus;
    private $carReadRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->queryBus = $this::getContainer()->get(QueryBusInterface::class);
        $this->carReadRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarReadRepo();
    }

    public function testGetAllCars(): void
    {
        $result = $this->queryBus->execute(new GetAllCarsQuery(0));

        $cars = $this->carReadRepo->findAllCarsEnabled(0);

        $carsArray = [];

        foreach ($cars as $car) {
            $carsArray[] = $car->toArray();
        }

        $this->assertSame($carsArray, $result);
    }
}
