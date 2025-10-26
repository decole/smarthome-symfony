<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\DeviceData\Service;

use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Relay\Entity\Relay;
use App\Tests\_support\Step\FunctionalStep\Domain\DeviceData\Service\DeviceDataResolverStep;
use Codeception\Attribute\Skip;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Skip('This test not support new version codeception')]
class DeviceDataResolverForRelayCest
{
    private ?Relay $device = null;

    private DeviceDataResolver $resolver;

    private DeviceDataValidationService $validateService;

    private DeviceDataCacheService $cacheService;

    public function _before(DeviceDataResolverStep $I): void
    {
        if (null === $this->device) {
            $this->device = $I->createDryRelayDevice();
        }

        $this->resolver = $I->grabService(DeviceDataResolver::class);
        $this->validateService = $I->grabService(DeviceDataValidationService::class);
        $this->cacheService = $I->grabService(DeviceDataCacheService::class);
    }

    public function positiveResolveDevicePayload(DeviceDataResolverStep $I): void
    {
        $payload = $this->device->getPayload();

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        $this->getResolver($event)->resolveDevicePayload(new DevicePayload($this->device->getTopic(), $payload));
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$this->device->getTopic()]);

        $I->assertEquals($payload, $cachedPayloadList[$this->device->getTopic()]);
    }

    public function negativeResolveDevicePayload(DeviceDataResolverStep $I): void
    {
        $payload = $I->faker()->word() . '__';

        $event = Stub::makeEmpty(EventDispatcherInterface::class, [
            'dispatch' => Expected::never(),
        ]);

        $this->getResolver($event)->resolveDevicePayload(new DevicePayload($this->device->getTopic(), $payload));
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$this->device->getTopic()]);

        $I->assertEquals($payload, $cachedPayloadList[$this->device->getTopic()]);
    }

    private function getResolver(EventDispatcherInterface $event): DeviceDataResolver
    {
        return new DeviceDataResolver(
            validateService: $this->validateService,
            cacheService: $this->cacheService,
            eventDispatcher: $event,
        );
    }
}
