<?php

namespace App\Domain\Factory;

use App\Domain\Repository\UserRepositoryInterface;

interface UserRepoFactoryInterface
{
    public function getUserReadRepo(): UserRepositoryInterface;
    public function getUserWriteRepo(): UserRepositoryInterface;
}
