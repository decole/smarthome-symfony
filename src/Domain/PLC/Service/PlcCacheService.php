<?php

namespace App\Domain\PLC\Service;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Contract\Repository\PlcRepositoryInterface;
use App\Domain\Contract\Service\CacheServiceInterface;
use App\Domain\PLC\Entity\PLC;
use App\Infrastructure\Cache\CacheService;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;

final class PlcCacheService implements CacheServiceInterface
{
    private const CACHE_MAP_KEY = 'plc_cache_map';

    public function __construct(
        private readonly PlcRepositoryInterface $repository,
        private readonly CacheService $cache,
    ) {
    }

    public function create(): void
    {
        $result = [];

        foreach ($this->repository->findAll(EntityStatusEnum::STATUS_ACTIVE->value) as $plc) {
            $result[$plc->getTargetTopic()] = $this->hydration($plc);
        }

        $this->cache->set(self::CACHE_MAP_KEY, $result);
    }

    public function getMap(): array
    {
        $cacheItem = $this->cache->getItem(self::CACHE_MAP_KEY);

        if ($cacheItem->get() === null) {
            $this->create();

            return $this->cache->get(self::CACHE_MAP_KEY);
        }

        return $cacheItem->get();
    }

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function set(string $key, mixed $value, int $lifetime = 0): void
    {
        $this->cache->set($key, $value, $lifetime);
    }

    private function hydration(PLC $controller): array
    {
        return [
            'controllerName' => $controller->getName(),
            'topic' => $controller->getTargetTopic(),
            'delay' => $controller->getAlarmSecondDelay(),
            'okMessage' => $controller->getStatusMessage()->getMessageOk(),
            'errorMessage'  => $controller->getStatusMessage()->getMessageWarn(),
            'isNotify' => $controller->isNotify(),
        ];
    }

    public function getCacheItem(string $cacheKey): CacheItem
    {
        return $this->cache->getItem($cacheKey);
    }
}