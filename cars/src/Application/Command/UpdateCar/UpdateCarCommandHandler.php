<?php

namespace App\Application\Command\UpdateCar;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\CarRepoFactoryInterface;
use DateTime;

class UpdateCarCommandHandler implements CommandHandlerInterface
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

    public function __invoke(UpdateCarCommand $updateCarCommand): array
    {
        $car = $this->carReadRepo->findOneCarById($updateCarCommand->id);

        if (is_null($car)) {
            return ['error' => true, 'status' => 'no car found!'];
        }

        $updateCarCommand->params['id'] = $car->getId();
        $car = $car->buildCarFromArray($car, $updateCarCommand->params);
        $car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));

        $this->carWriteRepo->update($car);

        if(get_class($this->carWriteRepo) !== get_class($this->carReadRepo)) {
            $this->carReadRepo->update($car);
        }

        $this->cacheClient->deleteIndex('car_'.$car->getId());
        $this->cacheClient->putIndex($car->toArray(), 'car_'.$car->getId());

        return ['error' => false, 'status' => 'car updated!', 'id' => $car->getId()];
    }
}
