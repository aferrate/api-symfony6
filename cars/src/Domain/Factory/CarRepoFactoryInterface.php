<?php

namespace App\Domain\Factory;

use App\Domain\Repository\CarRepositoryInterface;

interface CarRepoFactoryInterface
{
    public function getCarReadRepo(): CarRepositoryInterface;
    public function getCarWriteRepo(): CarRepositoryInterface;
}
