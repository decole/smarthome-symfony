<?php

namespace App\Tests\functional\Infrastructure\Mqtt\Service;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Tests\FunctionalTester;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use ArgumentCountError;
use Codeception\Stub;
use Codeception\Stub\Expected;
use DG\BypassFinals;
use Mosquitto\Message;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MqttHandleServiceCest
{
    public function process(FunctionalTester $I): void
    {
        BypassFinals::enable();

        $logger = Stub::makeEmpty(NullLogger::class, [
            'info' => Expected::never(),
        ]);

        $service = new MqttHandleService(
            client: Stub::make(StubMqttClient::class),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: $logger
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $service->process($this->createMessage($topic, $payload));
    }

    public function negativeProcessEmptyMessage(FunctionalTester $I): void
    {
        BypassFinals::enable();

        $logger = Stub::makeEmpty(NullLogger::class, [
            'info' => Expected::once(),
        ]);

        $service = new MqttHandleService(
            client: Stub::make(StubMqttClient::class),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: $logger
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $service->process(new Message());
    }

    public function negativeProcessWrongType(FunctionalTester $I): void
    {
        BypassFinals::enable();

        $logger = Stub::makeEmpty(NullLogger::class, [
            'info' => Expected::once(),
        ]);

        $service = new MqttHandleService(
            client: Stub::make(StubMqttClient::class),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: $logger
        );

        $I->expectThrowable(
            ArgumentCountError::class,
            fn () => $service->process()
        );
    }

    private function createMessage(string $topic, mixed $payload): Message
    {
        $message = new Message();
        $message->topic = $topic;
        $message->payload = $payload;
        $message->mid = 0;
        $message->qos = 2;
        $message->retain = false;

        return $message;
    }
}