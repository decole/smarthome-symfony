<?php

namespace App\Application\Service\DeviceData;

use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Infrastructure\Cache\CacheKeyListEnum;
use App\Infrastructure\Cache\CacheService;
use Symfony\Contracts\Cache\ItemInterface;

final class DeviceCacheService
{
    public function __construct(
        private CacheService $cache,
        private SensorRepositoryInterface $sensorRepository,
        private RelayRepositoryInterface $relayRepository,
        private SecurityRepositoryInterface $securityRepository,
        private FireSecurityRepositoryInterface $fireSecurityRepository
    ) {
    }

    public function create(): void
    {
        $this->cache->set(CacheKeyListEnum::DEVICE_MAP_CACHE, $this->getMap());
        $this->cache->delete([CacheKeyListEnum::DEVICE_TOPIC_BY_TYPE]);
        $this->getTopicMapByDeviceType();
    }

    public function getDeviceMap(): array
    {
        return $this->cache->getOrSet(
            key: CacheKeyListEnum::DEVICE_MAP_CACHE,
            callback: function (ItemInterface $item) {
                return $this->getMap();
            }
        );
    }

    public function getTopicMapByDeviceType(): array
    {
        return $this->cache->getOrSet(
            key: CacheKeyListEnum::DEVICE_TOPIC_BY_TYPE,
            callback: function (ItemInterface $item) {
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

    private function getMap(): array
    {
        return [
            Sensor::alias() => $this->sensorRepository->findAll(Sensor::STATUS_ACTIVE),
            Relay::alias() => $this->relayRepository->findAll(Relay::STATUS_ACTIVE),
            Security::alias() => $this->securityRepository->findAll(Security::STATUS_ACTIVE),
            FireSecurity::alias() => $this->fireSecurityRepository->findAll(FireSecurity::STATUS_ACTIVE),
        ];
    }
}