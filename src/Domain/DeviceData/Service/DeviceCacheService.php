<?php

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
        private CacheService $cache,
        private SensorRepositoryInterface $sensorRepository,
        private RelayRepositoryInterface $relayRepository,
        private SecurityRepositoryInterface $securityRepository,
        private FireSecurityRepositoryInterface $fireSecurityRepository
    ) {
    }

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function create(): void
    {
        $this->cache->set(CacheKeyListEnum::DEVICE_MAP_CACHE, $this->getMap());
        $this->cache->delete([CacheKeyListEnum::DEVICE_TOPIC_BY_TYPE]);
        $this->getTopicMapByDeviceTopic();
    }

    /**
     * @return array{"sensor":list<Sensor>, "relay":list<Relay>, "security":list<Security>, "fireSecurity":list<FireSecurity>}
     * @throws InvalidArgumentException
     */
    public function getDeviceMap(): array
    {
        return $this->cache->getOrSet(
            key: CacheKeyListEnum::DEVICE_MAP_CACHE,
            callback: function (ItemInterface $item) {
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