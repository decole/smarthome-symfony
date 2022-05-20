<?php


namespace App\Infrastructure\Cache;


use DateInterval;
use Psr\Cache\CacheException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CacheService
{
    public function __construct(private RedisTagAwareAdapter $cache)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $key): ?string
    {
        return $this->cache->getItem($key)->get();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getOrSet(string $key, callable $callback): ?string
    {
        return $this->cache->get($key, $callback);
    }

    /**
     * @throws CacheException|InvalidArgumentException
     */
    public function set(string $key, mixed $value, ?array $tags = null, int $lifetime = 0): void
    {
        $this->cache->get(
            $key,
            function (CacheItemInterface $item) use ($value, $tags, $lifetime)
            {
                $item->set($value);

                if ($tags !== null) {
                    $item->tag($tags);
                }

                if ($lifetime !== 0) {
                    $item->expiresAfter(new DateInterval("PT{$lifetime}S"));
                }

                return $item->get();
            }
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(array $key): bool
    {
        return $this->cache->deleteItems($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clearByTags(array $tags): void
    {
        $this->cache->invalidateTags($tags);
    }

    public function clearAll(): void
    {
        $this->cache->clear();
    }
}