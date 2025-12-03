<?php

namespace App\Tests\Unit\Command;

use App\Application\Command\CreateCar\CreateCarCommandHandler;
use App\Application\Command\CreateCar\CreateCarCommand;
use App\Factory\CacheFactory;
use App\Factory\CarRepoFactory;
use App\Doctrine\Repository\CarRepository as CarWriteRepository;
use App\Elasticsearch\Repository\CarRepository as CarReadRepository;
use App\Domain\Model\Car;
use App\Services\CacheRedis;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DateTime;

class CreateCarCommandHandlerTest extends TestCase
{
    private $carRepoFactoryMock;
    private $cacheFactoryMock;
    private $carWriteRepoMock;
    private $carReadRepoMock;
    private $cacheClientMock;
    private $handler;

    protected function setUp(): void
    {
        $this->carRepoFactoryMock = $this->createMock(CarRepoFactory::class);
        $this->cacheFactoryMock = $this->createMock(CacheFactory::class);
        $this->carWriteRepoMock = $this->createMock(CarWriteRepository::class);
        $this->carReadRepoMock = $this->createMock(CarReadRepository::class);
        $this->cacheClientMock = $this->createMock(CacheRedis::class);

        $this->carRepoFactoryMock->method('getCarWriteRepo')->willReturn($this->carWriteRepoMock);
        $this->carRepoFactoryMock->method('getCarReadRepo')->willReturn($this->carReadRepoMock);
        $this->cacheFactoryMock->method('getCache')->willReturn($this->cacheClientMock);

        $this->handler = new CreateCarCommandHandler($this->carRepoFactoryMock, $this->cacheFactoryMock);
    }

    public function testHandleCreatesCarSuccessfully()
    {
        $car = $this->createMock(Car::class);
        $car->method('getId')->willReturn('1');
        $car->method('toArray')->willReturn(['id' => 1, 'model' => 'Sedan']);
        $car->method('setCreatedAt')->willReturnSelf();
        $car->method('setUpdatedAt')->willReturnSelf();

        $createCarCommand = $this->createMock(CreateCarCommand::class);
        $createCarCommand->car = $car;

        $this->carWriteRepoMock->expects($this->once())
            ->method('save')
            ->with($car);

        $this->carReadRepoMock->expects($this->once())
            ->method('save')
            ->with($car);

        $this->cacheClientMock->expects($this->once())
            ->method('putIndex')
            ->with($car->toArray(), 'car_1');

        $result = ($this->handler)($createCarCommand);

        $this->assertEquals(['error' => false, 'status' => 'car created!', 'id' => 1], $result);
    }

    public function testHandleCreatesCarWithDifferentReadWriteRepos()
    {
        $car = $this->createMock(Car::class);
        $car->method('getId')->willReturn('2');
        $car->method('toArray')->willReturn(['id' => '2', 'model' => 'SUV']);
        $car->method('setCreatedAt')->willReturnSelf();
        $car->method('setUpdatedAt')->willReturnSelf();

        $createCarCommand = $this->createMock(CreateCarCommand::class);
        $createCarCommand->car = $car;

        $this->carWriteRepoMock->expects($this->once())
            ->method('save')
            ->with($car);

        $this->carReadRepoMock->expects($this->once())
            ->method('save')
            ->with($car);

        $this->cacheClientMock->expects($this->once())
            ->method('putIndex')
            ->with($car->toArray(), 'car_2');

        $result = ($this->handler)($createCarCommand);

        $this->assertEquals(['error' => false, 'status' => 'car created!', 'id' => 2], $result);
    }

    public function testHandleFailsWhenCarIsNull()
    {
        $this->expectException(\TypeError::class);

        $createCarCommand = $this->createMock(CreateCarCommand::class);
        $createCarCommand->car = null;

        ($this->handler)($createCarCommand);
    }
}
