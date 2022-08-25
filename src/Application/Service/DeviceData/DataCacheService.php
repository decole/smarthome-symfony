<?php

namespace App\Application\Service\DeviceData;

use App\Domain\Payload\DevicePayload;
use App\Infrastructure\Cache\CacheKeyListEnum;
use App\Infrastructure\Cache\CacheService;

/**
 * Кэширует данные переданные устройствами
 */
final class DataCacheService
{
    private const CACHE_LIMIT = 120;

    private const LIST_KEY = 'list';

    public function __construct(private CacheService $cache)
    {
    }

    public function save(DevicePayload $message): void
    {
        $map = $this->cache->get(CacheKeyListEnum::DEVICE_TOPICS_LIST) ?? [];

        $map[self::LIST_KEY][$message->getTopic()] = [
            'payload' => $message->getPayload(),
            'createdAt' => time(),
        ];

        $this->cache->set(
            key: CacheKeyListEnum::DEVICE_TOPICS_LIST,
            value: $map,
            tags: [CacheKeyListEnum::DEVICE_TOPICS_LIST],
            lifetime: self::CACHE_LIMIT
        );
    }

    public function getList(): array
    {
        return $this->cache->get(CacheKeyListEnum::DEVICE_TOPICS_LIST)[self::LIST_KEY] ?? [];
    }
}