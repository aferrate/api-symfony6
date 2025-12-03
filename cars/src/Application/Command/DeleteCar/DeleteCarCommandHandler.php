<?php

namespace App\Application\Command\DeleteCar;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\CarRepoFactoryInterface;

class DeleteCarCommandHandler implements CommandHandlerInterface
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

    public function __invoke(DeleteCarCommand $deleteCarCommand): array
    {
        $car = $this->carReadRepo->findOneCarById($deleteCarCommand->id);

        if(is_null($car)) {
            return ['error' => true, 'status' => 'no car found!'];
        }

        $this->carWriteRepo->delete($car);

        if(get_class($this->carWriteRepo) !== get_class($this->carReadRepo)) {
            $this->carReadRepo->delete($car);
        }

        $this->cacheClient->deleteIndex('car_'.$car->getId());

        return ['error' => false, 'status' => 'car deleted!', 'id' => $car->getId()];
    }
}
