<?php

namespace App\Domain\Validator\Request\Car;

interface ValidatorCarRequestInterface
{
    public function validateAddCarRequest(array $data): bool;
    public function validateUpdateCarRequest(array $data): bool;
    public function validateDeleteCarRequest(string $id): bool;
    public function validateGetAllCarsRequest(int $page): bool;
    public function validateGetAllCarsEnabledRequest(int $page): bool;
    public function validateGetCarFromIdRequest(string $id): bool;
}
