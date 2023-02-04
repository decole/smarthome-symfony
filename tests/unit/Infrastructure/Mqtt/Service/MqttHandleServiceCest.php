<?php

namespace App\Tests\unit\Infrastructure\Mqtt\Service;

use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use App\Tests\UnitTester;
use ArgumentCountError;
use Codeception\Stub;
use Codeception\Stub\Expected;
use DG\BypassFinals;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MqttHandleServiceCest
{
    public function finalClass(UnitTester $I): void
    {
        BypassFinals::enable();

        $service = $I->grabService(DeviceDataResolver::class);
        $I->assertInstanceOf(DeviceDataResolver::class, $service);
    }

    public function positivePost(UnitTester $I): void
    {
        BypassFinals::enable();

        $service = new MqttHandleService(
            client: Stub::make(StubMqttClient::class, [
                'connect' => Expected::once(),
                'subscribe' => Expected::once(),
                'onMessage' => Expected::once(),
                'loop' => Expected::once(),
            ]),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: Stub::makeEmpty(NullLogger::class, ['info' => Expected::never()])
        );

        $service->post(new DevicePayload(topic: $I->faker()->word(), payload: $I->faker()->word()));
    }

    public function negativePost(UnitTester $I): void
    {
        BypassFinals::enable();

        $service = new MqttHandleService(
            client: Stub::make(StubMqttClient::class, [
                'connect' => Expected::once(),
                'subscribe' => Expected::once(),
                'onMessage' => Expected::once(),
                'loop' => Expected::once(),
            ]),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: Stub::makeEmpty(NullLogger::class, ['info' => Expected::never()])
        );

        $I->expectThrowable(ArgumentCountError::class, fn () => $service->post());
    }
}