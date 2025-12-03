<?php

namespace App\Services;

use App\Domain\Cache\CacheInterface;
use Psr\Log\LoggerInterface;

class CacheDecoratorLog implements CacheInterface
{
    private $cacheClient;
    private $logger;

    public function __construct(CacheInterface $cacheRedis, LoggerInterface $logger)
    {
        $this->cacheClient = $cacheRedis;
        $this->logger = $logger;
    }

    public function putIndex(array $value, string $key): void
    {
        $this->cacheClient->putIndex($value, $key);
        $this->logger->info('entry added to cache');
    }

    public function getIndex(string $key): string
    {
        $this->logger->info('entry retrieved to cache');
        return $this->cacheClient->getIndex($key);
    }

    public function deleteIndex(string $key): void
    {
        $this->cacheClient->deleteIndex($key);
        $this->logger->info('entry removed from cache');
    }
}
