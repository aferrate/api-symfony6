<?php

namespace App\Factory;

use App\Domain\Factory\CacheFactoryInterface;
use App\Domain\Cache\CacheInterface;

class CacheFactory implements CacheFactoryInterface
{
    private $cache;
    private $cacheLogger;

    public function __construct(CacheInterface $cacheRedis, CacheInterface $cacheLogger)
    {
        $this->cache = $cacheRedis;
        $this->cacheLogger = $cacheLogger;
    }

    public function getCache(): CacheInterface
    {
        switch ($_ENV['CACHE']) {
            case "logger":
                $cache = $this->cacheLogger;
                break;
            case "default":
                $cache = $this->cache;
                break;
        }

        return $cache;
    }
}
