<?php

declare(strict_types=1);

namespace App\Domain\DeviceData\Service;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Contract\Service\CacheServiceInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Cache\CacheKeyListEnum;
use App\Infrastructure\Cache\CacheService;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

final class DeviceCacheService implements CacheServiceInterface
{
    public function __construct(
        private readonly CacheService $cache,
        private readonly SensorRepositoryInterface $sensorRepository,
        private readonly RelayRepositoryInterface $relayRepository,
        private readonly SecurityRepositoryInterface $securityRepository,
        private readonly FireSecurityRepositoryInterface $fireSecurityRepository
    ) {
    }

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function create(): void
    {
        $this->cache->set(CacheKeyListEnum::DEVICE_MAP_CACHE->value, $this->getMap());
        $this->cache->delete([CacheKeyListEnum::DEVICE_TOPIC_BY_TYPE->value]);
        $this->getTopicMapByDeviceTopic();
    }

    /**
     * @return array{"sensor":list<Sensor>, "relay":list<Relay>, "security":list<Security>, "fireSecurity":list<FireSecurity>}
     * @throws InvalidArgumentException
     */
    public function getDeviceMap(): array
    {
        return $this->cache->getOrSet(
            key: CacheKeyListEnum::DEVICE_MAP_CACHE->value,
            callback: function (ItemInterface $item): array {
                return $this->getMap();
            }
        );
    }

    /**
     * @return array<string, EntityInterface>
     * @throws InvalidArgumentException
     */
    public function getTopicMapByDeviceTopic(): array
    {
        return $this->cache->getOrSet(
            key: CacheKeyListEnum::DEVICE_TOPIC_BY_TYPE->value,
            callback: function (ItemInterface $item): array {
                $topicList = [];
                $map = $this->getDeviceMap();

                foreach ($map as $type => $devices) {
                    /** @var Sensor|Relay|FireSecurity|Security $device */
                    foreach ($devices as $device) {
                        if ($type === Relay::alias()) {
                            $topicList[$device->getCheckTopic()] = $device;

                            continue;
                        }

                        $topicList[$device->getTopic()] = $device;
                    }
                }

                return $topicList;
            }
        );
    }

    /**
     * @return array{"sensor":list<Sensor>, "relay":list<Relay>, "security":list<Security>, "fireSecurity":list<FireSecurity>}
     */
    private function getMap(): array
    {
        return [
            Sensor::alias() => $this->sensorRepository->findAll(),
            Relay::alias() => $this->relayRepository->findAll(),
            Security::alias() => $this->securityRepository->findAll(),
            FireSecurity::alias() => $this->fireSecurityRepository->findAll(),
        ];
    }
}