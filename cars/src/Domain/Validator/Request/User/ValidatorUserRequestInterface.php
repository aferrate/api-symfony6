<?php

namespace App\Domain\Validator\Request\User;

interface ValidatorUserRequestInterface
{
    public function validateAddUserRequest(array $data): bool;
    public function validateUpdateUserRequest(array $data): bool;
    public function validateDeleteUserRequest(string $id): bool;
    public function validateGetAllUsersRequest(int $page): bool;
    public function validateGetUserFromEmailRequest(string $id): bool;
}
