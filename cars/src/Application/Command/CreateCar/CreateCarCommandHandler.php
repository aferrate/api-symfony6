<?php

namespace App\Application\Command\CreateCar;

use App\Domain\Command\CommandHandlerInterface;
use App\Domain\Factory\CarRepoFactoryInterface;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Event\CarCreatedEvent;
use DateTime;

class CreateCarCommandHandler implements CommandHandlerInterface
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

    public function __invoke(CreateCarCommand $createCarCommand): array
    {
        $createCarCommand->car->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
        $createCarCommand->car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));

        $this->carWriteRepo->save($createCarCommand->car);

        if(get_class($this->carWriteRepo) !== get_class($this->carReadRepo)) {
            $this->carReadRepo->save($createCarCommand->car);
        }

        $this->eventDispatcher->dispatch(new CarCreatedEvent($createCarCommand->car));

        return ['error' => false, 'status' => 'car created!', 'id' => $createCarCommand->car->getId()];
    }
}
