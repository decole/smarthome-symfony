<?php

declare(strict_types=1);

namespace App\Domain\DeviceData\Service;

use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Cache\CacheService;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;

/**
 * Кэширует данные переданные устройствами.
 */
final class DeviceDataCacheService
{
    private const CACHE_LIMIT = 320;
    private const PREFIX = 'topic';

    public function __construct(private readonly CacheService $cache) {}

    /**
     * @throws InvalidArgumentException|CacheException
     */
    public function save(DevicePayload $message): void
    {
        $this->cache->set(
            key: $this->topicSeparate($message->getTopic()),
            value: $message->getPayload(),
            lifetime: self::CACHE_LIMIT,
        );
    }

    /**
     * @param list<string> $topics
     *
     * @throws InvalidArgumentException
     *
     * @return array<string, mixed>
     *
     * @deprecated  refactoring
     */
    public function getPayloadByTopicList(array $topics): array
    {
        $result = [];

        foreach ($topics as $topic) {
            $result[$topic] = $this->getTopicPayload(topic: mb_trim($topic));
        }

        return $result;
    }

    private function getTopicPayload(mixed $topic): ?string
    {
        return $this->cache->get($this->topicSeparate((string) $topic)) ?? null;
    }

    private function topicSeparate(mixed $topic): string
    {
        return \sprintf('%s_%s', self::PREFIX, str_replace(['/', '#'], '_', (string) $topic));
    }
}
