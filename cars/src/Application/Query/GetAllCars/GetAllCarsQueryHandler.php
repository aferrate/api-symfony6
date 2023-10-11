<?php

namespace App\Application\Query\GetAllCars;

use App\Domain\Factory\CarRepoFactoryInterface;
use App\Domain\Query\QueryHandlerInterface;

class GetAllCarsQueryHandler implements QueryHandlerInterface
{
    private $carReadRepo;

    public function __construct(CarRepoFactoryInterface $carRepoFactory)
    {
        $this->carReadRepo = $carRepoFactory->getCarReadRepo();
    }

    public function __invoke(GetAllCarsQuery $getAllCarsQuery): array
    {
        $cars = $this->carReadRepo->findAllCars($getAllCarsQuery->page);

        if(empty($cars)) {
            return [];
        }

        $carsArray = [];

        foreach ($cars as $car) {
            $carsArray[] = $car->toArray();
        }

        return $carsArray;
    }
}
