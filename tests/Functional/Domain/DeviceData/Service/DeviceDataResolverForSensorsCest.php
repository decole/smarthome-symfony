<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\DeviceData\Service;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Tests\Support\Step\FunctionalStep\Domain\DeviceData\Service\DeviceDataResolverStep;
use Codeception\Attribute\Examples;
use Codeception\Example;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeviceDataResolverForSensorsCest
{
    /**
     * @var EntityInterface[]
     */
    private ?array $list = null;

    private DeviceDataValidationService $validateService;

    private DeviceDataCacheService $cacheService;

    public function _before(DeviceDataResolverStep $I): void
    {
        if (null === $this->list) {
            $this->list = $I->createAllTypeSensors();
        }
        $this->validateService = $I->grabService(DeviceDataValidationService::class);
        $this->cacheService = $I->grabService(DeviceDataCacheService::class);
    }

    #[Examples('temperature', '50')]
    #[Examples('humidity', '90')]
    #[Examples('leakage', '60')]
    #[Examples('dryContact', '0')]
    public function positiveResolveDevicePayload(DeviceDataResolverStep $I, Example $example): void
    {
        $type = $example[0];
        $payloadExample = $example[1];
        $sensor = $this->list[$type];

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);

        $dto = new DevicePayload($sensor->getTopic(), $payloadExample);
        $this->getResolver($event)->resolveDevicePayload($dto);
        $cachedPayloadList = $this->cacheService->getPayloadByTopicList([$sensor->getTopic()]);

        $I->assertEquals($payloadExample, $cachedPayloadList[$sensor->getTopic()]);
    }

    #[Examples('temperature', '200')]
    #[Examples('humidity', '-20')]
    #[Examples('pressure', '999')]
    #[Examples('leakage', '1')]
    #[Examples('dryContact', '1')]
    public function negativeResolveDevicePayload(DeviceDataResolverStep $I, Example $example): void
    {
        $type = $example[0];
        $payloadExample = $example[1];
        $sensor = $this->list[$type];

        $event = Stub::makeEmpty(EventDispatcherInterface::class, [
            'dispatch' => Expected::exactly(2, fn () => (object) []),
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
            deviceDataCacheService: $this->cacheService,
            eventDispatcher: $event,
        );
    }
}
