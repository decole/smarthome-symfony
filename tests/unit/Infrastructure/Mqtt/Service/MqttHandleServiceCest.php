<?php

namespace App\Tests\unit\Infrastructure\Mqtt\Service;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use App\Tests\UnitTester;
use ArgumentCountError;
use Codeception\Stub;
use Codeception\Stub\Expected;
use DG\BypassFinals;
use Mosquitto\Message;
use Psr\Log\NullLogger;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MqttHandleServiceCest
{
    public function finalClass(UnitTester $I): void
    {
        BypassFinals::enable();

        $service = $I->grabService(DeviceDataResolver::class);
        $I->assertInstanceOf(DeviceDataResolver::class, $service);
    }

    public function createDto(UnitTester $I): void
    {
        BypassFinals::enable();

        $service = new MqttHandleService(
            client: Stub::make(StubMqttClient::class),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: Stub::makeEmpty(NullLogger::class, ['info' => Expected::never()])
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $reflection = new ReflectionMethod($service, 'createPayload');
        $reflection->setAccessible(true);
        $result = $reflection->invokeArgs($service, [$this->createMessage($topic, $payload)]);

        $I->assertInstanceOf(DevicePayload::class, $result);
        $I->assertEquals($topic, $result->getTopic());
        $I->assertEquals($payload, $result->getPayload());
    }

    public function positiveListen(UnitTester $I): void
    {
        BypassFinals::enable();

        $service = new MqttHandleService(
            client: Stub::make(StubMqttClient::class, [
                'connect' => Expected::once(),
                'subscribe' => Expected::once(),
                'onMessage' => Expected::once(),
                'loop' => Expected::once(),
            ]),
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: Stub::makeEmpty(NullLogger::class, ['info' => Expected::never()])
        );

        $service->listen();
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
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
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
            resolver: $I->grabService(DeviceDataResolver::class),
            deviceCacheService: $I->grabService(DeviceCacheService::class),
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: Stub::makeEmpty(NullLogger::class, ['info' => Expected::never()])
        );

        $I->expectThrowable(ArgumentCountError::class, fn () => $service->post());
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