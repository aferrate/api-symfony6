<?php

namespace App\Application\Command\UpdateCar;

use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\CarRepoFactoryInterface;
use DateTime;
use App\Domain\Exception\CarNotFoundException;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Event\CarUpdatedEvent;

class UpdateCarCommandHandler implements CommandHandlerInterface
{
    private $carReadRepo;
    private $carWriteRepo;
    private $eventDispatcher;

    public function __construct(CarRepoFactoryInterface $carRepoFactory, DomainEventDispatcherInterface $eventDispatcher)
    {
        $this->carReadRepo = $carRepoFactory->getCarReadRepo();
        $this->carWriteRepo = $carRepoFactory->getCarWriteRepo();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateCarCommand $updateCarCommand): array
    {
        $car = $this->carReadRepo->findOneCarById($updateCarCommand->id);

        if (is_null($car)) {
            throw new CarNotFoundException();
        }

        $updateCarCommand->params['id'] = $car->getId();
        $car = $car->buildCarFromArray($car, $updateCarCommand->params);
        $car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));

        $this->carWriteRepo->update($car);

        if(get_class($this->carWriteRepo) !== get_class($this->carReadRepo)) {
            $this->carReadRepo->update($car);
        }

        $this->eventDispatcher->dispatch(new CarUpdatedEvent($car));

        return ['error' => false, 'status' => 'car updated!', 'id' => $car->getId()];
    }
}
