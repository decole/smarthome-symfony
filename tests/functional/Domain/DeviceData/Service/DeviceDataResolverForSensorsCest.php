<?php

namespace App\Tests\functional\Domain\DeviceData\Service;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Tests\_support\Step\FunctionalStep\Domain\DeviceData\Service\DeviceDataResolverStep;
use Codeception\Example;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeviceDataResolverForSensorsCest
{
    /**
     * @var EntityInterface[]
     */
    private ?array $list = null;
    private DeviceDataResolver $resolver;
    private DeviceDataValidationService $validateService;
    private DeviceDataCacheService $cacheService;

    public function _before(DeviceDataResolverStep $I): void
    {
        if ($this->list === null) {
            $this->list = $I->createAllTypeSensors();
        }

        $this->resolver = $I->grabService(DeviceDataResolver::class);
        $this->validateService = $I->grabService(DeviceDataValidationService::class);
        $this->cacheService = $I->grabService(DeviceDataCacheService::class);
    }

    /**
     * @param DeviceDataResolverStep $I
     * @param Example $example
     * @example(type="temperature",payload="50")
     * @example(type="humidity",payload="90")
     * @example(type="pressure",payload="60")
     * @example(type="leakage",payload="0")
     * @example(type="dryContact",payload="0")
     * @return void
     * @throws InvalidArgumentException
     */
    public function positiveResolveDevicePayload(DeviceDataResolverStep $I, Example $example): void
    {
        $payloadExample = $example['payload'];
        $type = $example['type'];
        $sensor = $this->list[$type];

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);

        $dto = new DevicePayload($sensor->getTopic(), $payloadExample);
        $this->getResolver($event)->resolveDevicePayload($dto);
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$sensor->getTopic()]);

        $I->assertEquals($payloadExample, $cachedPayloadList[$sensor->getTopic()]);
    }

    /**
     * @param DeviceDataResolverStep $I
     * @param Example $example
     * @example(type="temperature",payload="200")
     * @example(type="humidity",payload="-20")
     * @example(type="pressure",payload="999")
     * @example(type="leakage",payload="1")
     * @example(type="dryContact",payload="1")
     * @return void
     * @throws InvalidArgumentException
     */
    public function negativeResolveDevicePayload(DeviceDataResolverStep $I, Example $example): void {
        $payloadExample = $example['payload'];
        $type = $example['type'];
        $sensor = $this->list[$type];

        $event = Stub::makeEmpty(EventDispatcherInterface::class, [
            'dispatch' => Expected::exactly(2, fn () => (object)[])
        ]);

        $dto = new DevicePayload($sensor->getTopic(), $payloadExample);
        $this->getResolver($event)->resolveDevicePayload($dto);
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$sensor->getTopic()]);

        $I->assertEquals($payloadExample, $cachedPayloadList[$sensor->getTopic()]);
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