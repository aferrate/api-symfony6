<?php

namespace App\Domain\Repository;

use App\Domain\Model\Car;

interface CarRepositoryInterface
{
    public function save(Car $car): Car;
    public function update(Car $car): Car;
    public function delete(Car $car): void;
    public function findAllCarsEnabled(int $page): array;
    public function findAllCars(int $page): array;
    public function findOneCarById(string $id): ?Car;
}
