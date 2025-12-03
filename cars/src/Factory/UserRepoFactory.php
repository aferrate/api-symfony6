<?php

namespace App\Factory;

use App\Domain\Factory\UserRepoFactoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

class UserRepoFactory implements UserRepoFactoryInterface
{
    private $userRepository;
    private $userRepoBackup;

    public function __construct(UserRepositoryInterface $userRepository, UserRepositoryInterface $userRepoBackup)
    {
        $this->userRepository = $userRepository;
        $this->userRepoBackup = $userRepoBackup;
    }

    public function getUserReadRepo(): UserRepositoryInterface
    {
        switch ($_ENV['READ_REPOSITORY']) {
            case "mysql":
                $userReadRepo = $this->userRepository;
                break;
            case "elasticsearch":
                $userReadRepo = $this->userRepoBackup;
                break;
        }

        return $userReadRepo;
    }

    public function getUserWriteRepo(): UserRepositoryInterface
    {
        switch ($_ENV['WRITE_REPOSITORY']) {
            case "mysql":
                $userWriteRepo = $this->userRepository;
                break;
            case "elasticsearch":
                $userWriteRepo = $this->userRepoBackup;
                break;
        }

        return $userWriteRepo;
    }
}
