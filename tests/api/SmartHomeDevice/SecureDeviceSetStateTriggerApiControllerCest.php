<?php

namespace App\Tests\api\SmartHomeDevice;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Tests\_support\Step\Api\SecureDeviceDataStep;
use Codeception\Stub;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SecureDeviceSetStateTriggerApiControllerCest
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

    public function positiveSetTriggerOn(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, false);
        $this->cache->create();

        $I->secureSetTrigger($device->getTopic(), true);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'topic' => $device->getTopic(),
            'trigger' => true,
        ]);
    }

    // фиктивный топик охранного датчика будет ложно указан как активный. для диверсификации
    public function wrongTopicSetTriggerOn(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, false);
        $this->cache->create();

        $wrongTopic = $device->getTopic() . $I->faker()->word();

        $I->secureSetTrigger($wrongTopic, true);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'topic' => $wrongTopic,
            'trigger' => true,
        ]);
    }

    public function positiveSetTriggerOff(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, true);
        $this->cache->create();

        $I->secureSetTrigger($device->getTopic(), false);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'topic' => $device->getTopic(),
            'trigger' => false,
        ]);
    }

    public function wrongTopicSetTriggerOff(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, true);
        $this->cache->create();

        $wrongTopic = $device->getTopic() . $I->faker()->word();

        $I->secureSetTrigger($wrongTopic, false);

        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'topic' => $wrongTopic,
            'trigger' => false,
        ]);
    }

    public function negativeSetTrigger(SecureDeviceDataStep $I): void
    {
        /** @var Secure $device */
        $device = $I->createSecureDevice(false, true);
        $this->cache->create();

        $I->secureSetTrigger($device->getTopic(), null);

        $I->seeResponseIsException();
        $I->seeResponseContainsJson([
            'error' => 'empty topic or trigger state',
        ]);
    }
}