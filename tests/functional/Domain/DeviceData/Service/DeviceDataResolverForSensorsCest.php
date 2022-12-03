<?php

namespace App\Tests\functional\Domain\DeviceData\Service;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\TemperatureSensor;
use App\Tests\_support\Step\FunctionalStep\Domain\DeviceData\Service\DeviceDataResolverStep;
use App\Tests\ExampleCest;
use Codeception\Example;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeviceDataResolverForSensorsCest
{
    /**
     * @var EntityInterface[]
     */
    private array $list;
    private DeviceDataResolver $resolver;
    private DeviceDataValidationService $validateService;
    private DeviceDataCacheService $cacheService;

    public function _before(DeviceDataResolverStep $I)
    {
        $this->list = $I->createAllTypeSensors($I);
        $this->resolver = $I->grabService(DeviceDataResolver::class);
        $this->validateService = $I->grabService(DeviceDataValidationService::class);
        $this->cacheService = $I->grabService(DeviceDataCacheService::class);
    }

    /**
     * @param DeviceDataResolverStep $I
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="pressure")
     * @example(type="leakage")
     * @example(type="dryContact")
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function positiveResolveDevicePayloadTypeTemperature(DeviceDataResolverStep $I, Example $example): void
    {
        $type = $example['type'];
        $payloadExample = $example['payload'];

        $sensor = $this->list[$type];
        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        // payloadMin: "0" payloadMax: "100"
        $payload = 50;
        $dto = new DevicePayload($sensor->getTopic(), $payload);
        $this->getResolver($event)->resolveDevicePayload($dto);

        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$sensor->getTopic()]);

        $I->assertEquals($payload, $cachedPayloadList[$sensor->getTopic()]);
    }

    public function negativeResolveDevicePayloadTypeTemperature(DeviceDataResolverStep $I): void {}
    public function negativeResolveDevicePayloadTypeHumidity(DeviceDataResolverStep $I): void {}
    public function negativeResolveDevicePayloadTypePressure(DeviceDataResolverStep $I): void {}
    public function negativeResolveDevicePayloadTypeLeakage(DeviceDataResolverStep $I): void {}
    public function negativeResolveDevicePayloadTypeDryContact(DeviceDataResolverStep $I): void {}

    private function getResolver(EventDispatcherInterface $event): DeviceDataResolver
    {
        return new DeviceDataResolver(
            validateService: $this->validateService,
            cacheService: $this->cacheService,
            eventDispatcher: $event
        );
    }
}