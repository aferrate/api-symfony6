<?php

namespace App\Services\Validator\Request\Car;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\Validator\Request\Car\ValidatorCarRequestInterface;

class ValidatorCarRequest implements ValidatorCarRequestInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateAddCarRequest(array $data): bool
    {
        $constraints = new Assert\Collection([
            'mark' => [
                new Assert\NotBlank()
            ],
            'model' => [
                new Assert\NotBlank()
            ],
            'description' => [
                new Assert\NotBlank()
            ],
            'country' => [
                new Assert\NotBlank()
            ],
            'city' => [
                new Assert\NotBlank()
            ],
            'year' => [
                new Assert\NotBlank()
            ],
            'enabled' => [
                new Assert\NotBlank()
            ],
            'imageFilename' => [
                new Assert\NotBlank()
            ]
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateUpdateCarRequest(array $data): bool
    {
        $constraints = new Assert\Collection([
            'mark' => [
                new Assert\NotBlank()
            ],
            'model' => [
                new Assert\NotBlank()
            ],
            'description' => [
                new Assert\NotBlank()
            ],
            'country' => [
                new Assert\NotBlank()
            ],
            'city' => [
                new Assert\NotBlank()
            ],
            'year' => [
                new Assert\NotBlank()
            ],
            'enabled' => [
                new Assert\NotNull()
            ],
            'imageFilename' => [
                new Assert\NotBlank()
            ]
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateDeleteCarRequest(string $id): bool
    {
        $constraints = new Assert\Collection([
            'id' => [
                new Assert\NotBlank()
            ],
        ]);

        $errors = $this->validator->validate(['id' => $id], $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateGetAllCarsRequest(int $page): bool
    {
        $constraints = new Assert\Collection([
            'page' => [
                new Assert\PositiveOrZero()
            ],
        ]);

        $errors = $this->validator->validate(['page' => $page], $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateGetAllCarsEnabledRequest(int $page): bool
    {
        $constraints = new Assert\Collection([
            'page' => [
                new Assert\PositiveOrZero()
            ],
        ]);

        $errors = $this->validator->validate(['page' => $page], $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateGetCarFromIdRequest(string $id): bool
    {
        $constraints = new Assert\Collection([
            'id' => [
                new Assert\NotBlank()
            ],
        ]);

        $errors = $this->validator->validate(['id' => $id], $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }
}
