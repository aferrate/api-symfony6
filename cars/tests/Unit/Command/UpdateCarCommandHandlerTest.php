<?php

namespace App\Tests\Unit\Command;

use App\Application\Command\UpdateCar\UpdateCarCommandHandler;
use App\Application\Command\UpdateCar\UpdateCarCommand;
use App\Infrastructure\Factory\CacheFactory;
use App\Infrastructure\Factory\CarRepoFactory;
use App\Infrastructure\Doctrine\Repository\CarRepository as CarWriteRepository;
use App\Infrastructure\Elasticsearch\Repository\CarRepository as CarReadRepository;
use App\Domain\Model\Car;
use App\Infrastructure\Services\CacheRedis;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DateTime;

class UpdateCarCommandHandlerTest extends TestCase
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

        $this->handler = new UpdateCarCommandHandler($this->carRepoFactoryMock, $this->cacheFactoryMock);
    }

    public function testUpdateCarSuccessfully()
    {
        $car = $this->createMock(Car::class);
        $car->method('getId')->willReturn('1');
        $car->method('toArray')->willReturn(['id' => 1, 'model' => 'Sedan']);
        $car->method('buildCarFromArray')->willReturn($car);
        $car->method('setUpdatedAt')->willReturnSelf();

        $updateCarCommand = $this->createMock(UpdateCarCommand::class);
        $updateCarCommand->id = 1;
        $updateCarCommand->params = ['model' => 'SUV'];

        $this->carReadRepoMock->expects($this->once())
            ->method('findOneCarById')
            ->with(1)
            ->willReturn($car);

        $this->carWriteRepoMock->expects($this->once())
            ->method('update')
            ->with($car);

        $this->carReadRepoMock->expects($this->once())
            ->method('update')
            ->with($car);

        $this->cacheClientMock->expects($this->once())
            ->method('deleteIndex')
            ->with('car_1');

        $this->cacheClientMock->expects($this->once())
            ->method('putIndex')
            ->with($car->toArray(), 'car_1');

        $result = ($this->handler)($updateCarCommand);

        $this->assertEquals(['error' => false, 'status' => 'car updated!', 'id' => 1], $result);
    }

    public function testUpdateCarWhenCarNotFound()
    {
        $updateCarCommand = $this->createMock(UpdateCarCommand::class);
        $updateCarCommand->id = 999;
        $updateCarCommand->params = ['model' => 'Coupe'];

        $this->carReadRepoMock->expects($this->once())
            ->method('findOneCarById')
            ->with(999)
            ->willReturn(null);

        $result = ($this->handler)($updateCarCommand);

        $this->assertEquals(['error' => true, 'status' => 'no car found!'], $result);
    }

    public function testUpdateCarFailsWhenCarIdIsNull()
    {
        $this->expectException(\TypeError::class);

        $updateCarCommand = $this->createMock(UpdateCarCommand::class);
        $updateCarCommand->id = null;
        $updateCarCommand->params = ['model' => 'Sedan'];

        ($this->handler)($updateCarCommand);
    }
}
