<?php

namespace App\Tests\functional\Domain\DeviceData\Service;

use App\Application\Service\Factory\DeviceAlertFactory;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Cache\CacheService;
use App\Tests\FunctionalTester;
use Psr\EventDispatcher\EventDispatcherInterface;

class DeviceDataResolverForSensorsCest
{
    private CacheService $cache;

    private DeviceDataResolver $resolver;

    public function positiveResolveDevicePayloadTypeTemperature(FunctionalTester $I): void
    {
        $topic = $I->faker()->word();
        $payload = $I->faker()->word();
        $dto = new DevicePayload($topic, $payload);
        $this->resolver->resolveDevicePayload($dto);

//        private DeviceDataCacheService $cacheService, - create Entity Sensor

        // private EventDispatcherInterface $eventDispatcher
        // check - $this->cacheService->save($payload);
        // validatePayload(DevicePayload $payload): void
        // if unNormal - if (!$resultDto->isNormal()) {
        //            (new DeviceAlertFactory($this->eventDispatcher))
        //                ->create($resultDto->getDevice(), $payload)
        //                ->notify();
        //        }

        // catch Event
    }

    public function positiveResolveDevicePayloadTypeHumidity(FunctionalTester $I): void {}
    public function positiveResolveDevicePayloadTypePressure(FunctionalTester $I): void {}
    public function positiveResolveDevicePayloadTypeLeakage(FunctionalTester $I): void {}
    public function positiveResolveDevicePayloadTypeDryContact(FunctionalTester $I): void {}

    public function negativeResolveDevicePayloadTypeTemperature(FunctionalTester $I): void {}
    public function negativeResolveDevicePayloadTypeHumidity(FunctionalTester $I): void {}
    public function negativeResolveDevicePayloadTypePressure(FunctionalTester $I): void {}
    public function negativeResolveDevicePayloadTypeLeakage(FunctionalTester $I): void {}
    public function negativeResolveDevicePayloadTypeDryContact(FunctionalTester $I): void {}
}