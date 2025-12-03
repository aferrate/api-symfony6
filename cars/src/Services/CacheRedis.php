<?php

namespace App\Services;

use App\Domain\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheRedis implements CacheInterface
{
    private $cacheClient;

    public function __construct()
    {
        $this->cacheClient = RedisAdapter::createConnection(
            $_ENV['REDIS_DSN']
        );

        $this->cacheClient->auth($_ENV['REDIS_PASSWORD']);
    }

    public function putIndex(array $value, string $key): void
    {
        $this->cacheClient->set($key, json_encode($value), ['nx', 'ex' => $_ENV['REDIS_CACHE_TTL']]);
    }

    public function getIndex(string $key): string
    {
        $cacheValue = $this->cacheClient->get($key);

        if (!empty($cacheValue)) {
            return $cacheValue;
        }

        return '';
    }

    public function deleteIndex(string $key): void
    {
        $this->cacheClient->del($key);
    }
}
