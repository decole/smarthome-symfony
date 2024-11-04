<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Symfony\Component\Cache\CacheItem;

interface CacheServiceInterface
{
    public function get(string $key): mixed;

    public function getItem(string $key): CacheItem;

    public function getOrSet(string $key, callable $callback): mixed;

    public function set(string $key, mixed $value, int $lifetime = 0): void;

    public function delete(array $key): bool;

    public function clear(): void;
}