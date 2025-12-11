<?php

namespace App\Application\Command\DeleteCar;

use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\CarRepoFactoryInterface;
use App\Domain\Exception\CarNotFoundException;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Event\CarDeletedEvent;

class DeleteCarCommandHandler implements CommandHandlerInterface
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

    public function __invoke(DeleteCarCommand $deleteCarCommand): array
    {
        $car = $this->carReadRepo->findOneCarById($deleteCarCommand->id);

        if(is_null($car)) {
            throw new CarNotFoundException();
        }

        $this->carWriteRepo->delete($car);

        if(get_class($this->carWriteRepo) !== get_class($this->carReadRepo)) {
            $this->carReadRepo->delete($car);
        }

        $this->eventDispatcher->dispatch(new CarDeletedEvent($deleteCarCommand->id));

        return ['error' => false, 'status' => 'car deleted!', 'id' => $car->getId()];
    }
}
