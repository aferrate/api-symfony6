<?php

namespace App\Application\Query\GetCarFromId;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Factory\CarRepoFactoryInterface;
use App\Domain\Query\QueryHandlerInterface;

class GetCarFromIdQueryHandler implements QueryHandlerInterface
{
    private $carReadRepo;
    private $cacheClient;

    public function __construct(CarRepoFactoryInterface $carRepoFactory, CacheFactoryInterface $cacheFactory)
    {
        $this->carReadRepo = $carRepoFactory->getCarReadRepo();
        $this->cacheClient = $cacheFactory->getCache();
    }

    public function __invoke(GetCarFromIdQuery $getCarFromIdQuery): array
    {
        $cacheCar = $this->cacheClient->getIndex('car_'.$getCarFromIdQuery->id);

        if($cacheCar) {
            return ['error' => false, 'data' => json_decode($cacheCar, true)];
        }

        $car = $this->carReadRepo->findOneCarById($getCarFromIdQuery->id);

        if(is_null($car)) {
            return ['error' => true, 'status' => 'no car found!'];
        }

        $this->cacheClient->putIndex($car->toArray(), 'car_'.$car->getId());

        return ['error' => false, 'data' => $car->toArray()];
    }
}
