<?php

namespace App\Tests\functional\Infrastructure\Mqtt\Service;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Tests\FunctionalTester;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use ArgumentCountError;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Mosquitto\Message;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\NullLogger;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MqttHandleServiceCest
{
    // саму библиотеку mosquitto-php мы не тестируем, только тот функционал, что написали сами
    public function process(FunctionalTester $I): void
    {
        $logger = Stub::makeEmpty(NullLogger::class, [
            'info' => \Codeception\Stub\Expected::never(),
        ]);

        $this->service = new MqttHandleService(
            instance: Stub::make(StubMqttClient::class),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: $logger,
            broker: $I->faker()->ipv4(),
            port: random_int(80, 99999)
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $this->service->process($this->createMessage($topic, $payload));
    }

    public function negativeProcessEmptyMessage(FunctionalTester $I): void
    {
        $logger = Stub::makeEmpty(NullLogger::class, [
            'info' => \Codeception\Stub\Expected::once(),
        ]);

        $this->service = new MqttHandleService(
            instance: Stub::make(StubMqttClient::class),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: $logger,
            broker: $I->faker()->ipv4(), port: random_int(80, 99999),
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $this->service->process(new Message());
    }

    public function negativeProcessWrongType(FunctionalTester $I): void
    {
        $logger = Stub::makeEmpty(NullLogger::class, [
            'info' => \Codeception\Stub\Expected::once(),
        ]);

        $this->service = new MqttHandleService(
            instance: Stub::make(StubMqttClient::class),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: $logger,
            broker: $I->faker()->ipv4(),
            port: random_int(80, 99999)
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $I->expectThrowable(
            ArgumentCountError::class,
            fn () => $this->service->process()
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