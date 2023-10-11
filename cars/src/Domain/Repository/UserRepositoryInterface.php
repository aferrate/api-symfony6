<?php

namespace App\Domain\Repository;

use App\Domain\Model\User;

interface UserRepositoryInterface
{
    public function findOneByEmail(string $value): ?User;
    public function save(User $user): string;
    public function update(User $user): User;
    public function delete(User $user): void;
    public function findAllUsers(int $page): array;
    public function checkEmailRepeated(string $email, string $id): ?User;
    public function getEmailUsers(): array;
}
