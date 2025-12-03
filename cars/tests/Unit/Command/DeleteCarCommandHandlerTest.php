<?php

namespace App\Tests\Unit\Command;

use App\Application\Command\DeleteCar\DeleteCarCommandHandler;
use App\Application\Command\DeleteCar\DeleteCarCommand;
use App\Factory\CacheFactory;
use App\Factory\CarRepoFactory;
use App\Doctrine\Repository\CarRepository as CarWriteRepository;
use App\Elasticsearch\Repository\CarRepository as CarReadRepository;
use App\Domain\Model\Car;
use App\Services\CacheRedis;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DeleteCarCommandHandlerTest extends TestCase
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

        $this->handler = new DeleteCarCommandHandler($this->carRepoFactoryMock, $this->cacheFactoryMock);
    }

    public function testDeleteCarSuccessfully()
    {
        $car = $this->createMock(Car::class);
        $car->method('getId')->willReturn('1');

        $deleteCarCommand = $this->createMock(DeleteCarCommand::class);
        $deleteCarCommand->id = 1;

        $this->carReadRepoMock->expects($this->once())
            ->method('findOneCarById')
            ->with(1)
            ->willReturn($car);

        $this->carWriteRepoMock->expects($this->once())
            ->method('delete')
            ->with($car);

        $this->carReadRepoMock->expects($this->once())
            ->method('delete')
            ->with($car);

        $this->cacheClientMock->expects($this->once())
            ->method('deleteIndex')
            ->with('car_1');

        $result = ($this->handler)($deleteCarCommand);

        $this->assertEquals(['error' => false, 'status' => 'car deleted!', 'id' => 1], $result);
    }

    public function testDeleteCarWhenCarNotFound()
    {
        $deleteCarCommand = $this->createMock(DeleteCarCommand::class);
        $deleteCarCommand->id = 999;

        $this->carReadRepoMock->expects($this->once())
            ->method('findOneCarById')
            ->with(999)
            ->willReturn(null);

        $result = ($this->handler)($deleteCarCommand);

        $this->assertEquals(['error' => true, 'status' => 'no car found!'], $result);
    }

    public function testDeleteCarFailsWhenCarIdIsNull()
    {
        $this->expectException(\TypeError::class);

        $deleteCarCommand = $this->createMock(DeleteCarCommand::class);
        $deleteCarCommand->id = null;

        ($this->handler)($deleteCarCommand);
    }
}
