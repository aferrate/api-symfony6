<?php

namespace App\Application\Command\CreateCar;

use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Factory\CarRepoFactoryInterface;
use DateTime;

class CreateCarCommandHandler implements CommandHandlerInterface
{
    private $carReadRepo;
    private $carWriteRepo;
    private $cacheClient;

    public function __construct(CarRepoFactoryInterface $carRepoFactory, CacheFactoryInterface $cacheFactory)
    {
        $this->carReadRepo = $carRepoFactory->getCarReadRepo();
        $this->carWriteRepo = $carRepoFactory->getCarWriteRepo();
        $this->cacheClient = $cacheFactory->getCache();
    }

    public function __invoke(CreateCarCommand $createCarCommand): array
    {
        $createCarCommand->car->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
        $createCarCommand->car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));

        $this->carWriteRepo->save($createCarCommand->car);

        if(get_class($this->carWriteRepo) !== get_class($this->carReadRepo)) {
            $this->carReadRepo->save($createCarCommand->car);
        }

        $this->cacheClient->putIndex($createCarCommand->car->toArray(), 'car_'.$createCarCommand->car->getId());

        return ['error' => false, 'status' => 'car created!', 'id' => $createCarCommand->car->getId()];
    }
}
