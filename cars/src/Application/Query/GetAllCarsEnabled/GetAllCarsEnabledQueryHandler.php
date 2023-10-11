<?php

namespace App\Application\Query\GetAllCarsEnabled;

use App\Domain\Factory\CarRepoFactoryInterface;
use App\Domain\Query\QueryHandlerInterface;

class GetAllCarsEnabledQueryHandler implements QueryHandlerInterface
{
    private $carReadRepo;

    public function __construct(CarRepoFactoryInterface $carRepoFactory)
    {
        $this->carReadRepo = $carRepoFactory->getCarReadRepo();
    }

    public function __invoke(GetAllCarsEnabledQuery $getAllCarsEnabledQuery): array
    {
        $cars = $this->carReadRepo->findAllCarsEnabled($getAllCarsEnabledQuery->page);

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
