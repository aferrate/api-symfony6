<?php

namespace App\Domain\Validator\Request\User;

interface ValidatorUserRequestInterface
{
    public function validateAddUserRequest(array $data): bool;
    public function validateUpdateUserRequest(array $data): bool;
    public function validateDeleteUserRequest(array $email): bool;
    public function validateGetAllUsersRequest(array $page): bool;
    public function validateGetUserFromEmailRequest(string $email): bool;
}
