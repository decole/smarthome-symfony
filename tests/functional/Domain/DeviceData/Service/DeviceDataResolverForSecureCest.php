<?php

namespace App\Tests\functional\Domain\DeviceData\Service;

use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Security\Entity\Security;
use App\Tests\_support\Step\FunctionalStep\Domain\DeviceData\Service\DeviceDataResolverStep;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeviceDataResolverForSecureCest
{
    private DeviceDataResolver $resolver;
    private DeviceDataValidationService $validateService;
    private DeviceDataCacheService $cacheService;

    public function _before(DeviceDataResolverStep $I): void
    {
        $this->resolver = $I->grabService(DeviceDataResolver::class);
        $this->validateService = $I->grabService(DeviceDataValidationService::class);
        $this->cacheService = $I->grabService(DeviceDataCacheService::class);
    }

    public function positiveResolveHoldDevicePayload(DeviceDataResolverStep $I): void
    {
        $device = $I->createSecurityDevice();
        $dto = $I->crudSecurityService()->entityByDto($device->getIdToString());
        $dto->lastCommand = Security::HOLD_STATE;
        $device = $I->crudSecurityService()->update($device->getIdToString(), $dto);

        $payload = $device->getHoldPayload();

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        $this->getResolver($event)->resolveDevicePayload(new DevicePayload($device->getTopic(), $payload));
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$device->getTopic()]);

        $I->assertEquals($payload, $cachedPayloadList[$device->getTopic()]);
    }

    public function positiveResolveHoldDeviceWithMovingTriggerPayload(DeviceDataResolverStep $I): void
    {
        $device = $I->createSecurityDevice();
        $dto = $I->crudSecurityService()->entityByDto($device->getIdToString());
        $dto->lastCommand = Security::HOLD_STATE;
        $device = $I->crudSecurityService()->update($device->getIdToString(), $dto);

        $payload = $device->getDetectPayload();

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        $this->getResolver($event)->resolveDevicePayload(new DevicePayload($device->getTopic(), $payload));
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$device->getTopic()]);

        $I->assertEquals($payload, $cachedPayloadList[$device->getTopic()]);
    }

    public function positiveResolveTriggeredDeviceWithMovingTriggerPayload(DeviceDataResolverStep $I): void
    {
        $device = $I->createSecurityDevice();
        $dto = $I->crudSecurityService()->entityByDto($device->getIdToString());
        $dto->lastCommand = Security::GUARD_STATE;
        $device = $I->crudSecurityService()->update($device->getIdToString(), $dto);

        $payload = $device->getDetectPayload();

        $event = Stub::makeEmpty(EventDispatcherInterface::class, [
            'dispatch' => Expected::exactly(3, fn () => (object)[])
        ]);
        $this->getResolver($event)->resolveDevicePayload(new DevicePayload($device->getTopic(), $payload));
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$device->getTopic()]);

        $I->assertEquals($payload, $cachedPayloadList[$device->getTopic()]);
    }

    public function positiveResolveTriggeredDeviceWithOutMovingTriggerPayload(DeviceDataResolverStep $I): void
    {
        $device = $I->createSecurityDevice();
        $dto = $I->crudSecurityService()->entityByDto($device->getIdToString());
        $dto->lastCommand = Security::GUARD_STATE;
        $device = $I->crudSecurityService()->update($device->getIdToString(), $dto);

        $payload = $device->getHoldPayload();

        $event = Stub::makeEmpty(EventDispatcherInterface::class, [
            'dispatch' => Expected::never()
        ]);
        $this->getResolver($event)->resolveDevicePayload(new DevicePayload($device->getTopic(), $payload));
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$device->getTopic()]);

        $I->assertEquals($payload, $cachedPayloadList[$device->getTopic()]);
    }

    private function getResolver(EventDispatcherInterface $event): DeviceDataResolver
    {
        return new DeviceDataResolver(
            validateService: $this->validateService,
            cacheService: $this->cacheService,
            eventDispatcher: $event
        );
    }
}