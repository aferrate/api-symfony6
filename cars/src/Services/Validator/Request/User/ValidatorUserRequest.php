<?php

namespace App\Services\Validator\Request\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\Validator\Request\User\ValidatorUserRequestInterface;

class ValidatorUserRequest implements  ValidatorUserRequestInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateAddUserRequest(array $data): bool
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\Email()
            ],
            'password' => [
                new Assert\NotBlank()
            ]
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateUpdateUserRequest(array $data): bool
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\Email()
            ],
            'password' => [
                new Assert\NotBlank()
            ]
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateDeleteUserRequest(array $email): bool
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\Email()
            ],
        ]);

        $errors = $this->validator->validate($email, $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateGetAllUsersRequest(array $page): bool
    {
        $constraints = new Assert\Collection([
            'page' => [
                new Assert\PositiveOrZero()
            ],
        ]);

        $errors = $this->validator->validate($page, $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }

    public function validateGetUserFromEmailRequest(string $email): bool
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\Email()
            ],
        ]);

        $errors = $this->validator->validate(['email' => $email], $constraints);

        if ($errors->count()) {
            return false;
        }

        return true;
    }
}
