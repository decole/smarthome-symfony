<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;

final class CacheService
{
    public function __construct(private readonly RedisAdapter $cache)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $key): mixed
    {
        return $this->cache->getItem($key)->get();
    }

    public function getItem(string $key): CacheItem
    {
        return $this->cache->getItem($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getOrSet(string $key, callable $callback): mixed
    {
        return $this->cache->get($key, $callback);
    }

    /**
     * @throws CacheException|InvalidArgumentException
     */
    public function set(string $key, mixed $value, int $lifetime = 0): void
    {
        /** @var CacheItem $item */
        $item = $this->cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($lifetime === 0 ? null : $lifetime);

        $this->cache->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(array $key): bool
    {
        return $this->cache->deleteItems($key);
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}