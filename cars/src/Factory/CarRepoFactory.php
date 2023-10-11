<?php

namespace App\Factory;

use App\Domain\Factory\CarRepoFactoryInterface;
use App\Domain\Repository\CarRepositoryInterface;

class CarRepoFactory implements CarRepoFactoryInterface
{
    private $carRepository;
    private $carRepoBackup;

    public function __construct(CarRepositoryInterface $carRepository, CarRepositoryInterface $carRepoBackup)
    {
        $this->carRepository = $carRepository;
        $this->carRepoBackup = $carRepoBackup;
    }

    public function getCarReadRepo(): CarRepositoryInterface
    {
        switch ($_ENV['READ_REPOSITORY']) {
            case "mysql":
                $carReadRepo = $this->carRepository;
                break;
            case "elasticsearch":
                $carReadRepo = $this->carRepoBackup;
                break;
        }

        return $carReadRepo;
    }

    public function getCarWriteRepo(): CarRepositoryInterface
    {
        switch ($_ENV['WRITE_REPOSITORY']) {
            case "mysql":
                $carWriteRepo = $this->carRepository;
                break;
            case "elasticsearch":
                $carWriteRepo = $this->carRepoBackup;
                break;
        }

        return $carWriteRepo;
    }
}
