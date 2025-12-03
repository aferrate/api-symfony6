<?php

namespace App\Domain\Factory;

use App\Domain\Cache\CacheInterface;

interface CacheFactoryInterface
{
    public function getCache(): CacheInterface;
}
