<?php

namespace App\Domain\DeviceData\Service;

use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Cache\CacheKeyListEnum;
use App\Infrastructure\Cache\CacheService;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;

/**
 * Кэширует данные переданные устройствами
 */
final class DeviceDataCacheService
{
    private const CACHE_LIMIT = 320;

    private const LIST_KEY = 'list';

    public function __construct(private CacheService $cache)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function save(DevicePayload $message): void
    {
        $map = $this->cache->get(CacheKeyListEnum::DEVICE_TOPICS_LIST) ?? [];

        $map[self::LIST_KEY][$message->getTopic()] = [
            'payload' => $message->getPayload(),
            'createdAt' => time(),
            'expiredAt' => time() + self::CACHE_LIMIT,
        ];

        $map = $this->clearOldPayload($map);

        $this->setCache($map);
    }

    /**
     * @param list<string> $topics
     * @return array<string, mixed>
     * @throws InvalidArgumentException
     */
    public function getPayloadByTopicList(array $topics): array
    {
        $result = [];

        foreach ($topics as $topic) {
            $result[$topic] = $this->getTopicPayload(cached: $this->getList(), topic: trim($topic));
        }

        return $result;
    }

    /**
     * @param list<string, array<string, mixed>> $map
     * @return array
     */
    public function clearOldPayload(array $map): array
    {
        foreach ($map[self::LIST_KEY] as $cachedTopic => $payload) {
            if ($this->isExpiredPayload($payload['createdAt'])) {
                unset($map[$cachedTopic]);
            }
        }

        return $map;
    }

    /**
     * @return list<string, array<string, mixed>>
     * @throws InvalidArgumentException
     */
    public function getList(): array
    {
        return $this->cache->get(CacheKeyListEnum::DEVICE_TOPICS_LIST)[self::LIST_KEY] ?? [];
    }

    private function getTopicPayload(array $cached, mixed $topic): ?string
    {
        foreach ($cached as $cachedTopic => $payload) {
            if (trim($cachedTopic) == trim($topic)) {
                return $payload['payload'];
            }
        }

        return null;
    }

    private function isExpiredPayload(int $payloadTime): bool
    {
        return time() > $payloadTime + self::CACHE_LIMIT;
    }

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    private function setCache(array $map): void
    {
        $this->cache->set(
            key: CacheKeyListEnum::DEVICE_TOPICS_LIST,
            value: $map,
            lifetime: self::CACHE_LIMIT
        );
    }
}