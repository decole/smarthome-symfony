<?php

namespace App\Tests\api\SmartHomeDevice;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Tests\_support\Step\Api\SecureDeviceDataStep;
use Codeception\Stub;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SecureDeviceTopicPayloadApiControllerCest
{
    private DeviceDataResolver $resolver;
    private DeviceCacheService $cache;

    public function _before(SecureDeviceDataStep $I): void
    {
        $this->cache = $I->grabService(DeviceCacheService::class);
        $this->resolver = new DeviceDataResolver(
            validateService: $I->grabService(DeviceDataValidationService::class),
            cacheService: $I->grabService(DeviceDataCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => fn () => (object)[]]),
        );
    }

    public function positiveGetTopicWithTrigger(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, true);
        $this->cache->create();
        $this->resolver->resolveDevicePayload(new DevicePayload($device->getTopic(), $device->getHoldPayload()));

        $topic = $device->getTopic();
        $I->secureDeviceState($topic);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'state' => false,
            'isTriggered' => true,
        ]);
    }

    public function positiveGetTopicWithoutTrigger(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, false);
        $this->cache->create();
        $this->resolver->resolveDevicePayload(new DevicePayload($device->getTopic(), $device->getHoldPayload()));

        $topic = $device->getTopic();
        $I->secureDeviceState($topic);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'state' => false,
            'isTriggered' => false,
        ]);
    }

    public function positiveGetTopicWithTriggerAndTriggeredDevice(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(true, true);
        $this->cache->create();
        $this->resolver->resolveDevicePayload(new DevicePayload($device->getTopic(), $device->getDetectPayload()));

        $topic = $device->getTopic();
        $I->secureDeviceState($topic);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'state' => true,
            'isTriggered' => true,
        ]);
    }

    // если будет происходить перебор и поиск топика системы безопасности - отдаем статус как неактивное устройство
    public function negativeGetWrongTopic(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, false);
        $this->cache->create();
        $this->resolver->resolveDevicePayload(new DevicePayload($device->getTopic(), $device->getHoldPayload()));

        $topic = $device->getTopic() . $I->faker()->word();
        $I->secureDeviceState($topic);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'state' => false,
            'isTriggered' => false,
        ]);
    }

    public function negativeGetEmptyTopic(SecureDeviceDataStep $I): void
    {
        $I->secureDeviceState(null);

        $I->seeResponseIsException();
        $I->seeResponseContainsJson([
            'error' => 'empty topics',
        ]);
    }
}