<?php

namespace App\Domain\Cache;

interface CacheInterface
{
    public function putIndex(array $value, string $key): void;
    public function getIndex(string $key): string;
    public function deleteIndex(string $key): void;
}
