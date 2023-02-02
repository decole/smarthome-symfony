<?php

namespace App\Tests\functional\Application\Consumer;

use App\Application\Consumer\MqttListenConsumer;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\DeviceData\Service\DeviceDataValidationService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Codeception\Stub;
use App\Tests\FunctionalTester;
use App\Tests\UnitTester;
use Codeception\Stub\Expected;
use DG\BypassFinals;
use Psr\Log\NullLogger;
use ReflectionMethod;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class MqttListenConsumerCest
{
    public function finalClass(UnitTester $I): void
    {
        BypassFinals::enable();

        $service = $I->grabService(DeviceDataResolver::class);
        $I->assertInstanceOf(DeviceDataResolver::class, $service);
    }

    public function createDto(FunctionalTester $I): void
    {
        BypassFinals::enable();

        $eventDispatcher = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);

        $consumer = new MqttListenConsumer(
            resolver: new DeviceDataResolver(
                validateService: $I->grabService(DeviceDataValidationService::class),
                cacheService: $I->grabService(DeviceDataCacheService::class),
                eventDispatcher: $eventDispatcher
            ),
            eventDispatcher: $eventDispatcher,
            serializer: $I->grabService(SerializerInterface::class),
            logger: Stub::makeEmpty(NullLogger::class, ['info' => Expected::never()])
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $params = [
            'topic' => $topic,
            'payload' => $payload,
        ];

        $message = new AMQPMessage(
            json_encode($params, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $reflection = new ReflectionMethod($consumer, 'getDto');
        $reflection->setAccessible(true);
        $result = $reflection->invokeArgs($consumer, [$message]);

        $I->assertInstanceOf(DevicePayload::class, $result);
        $I->assertEquals($topic, $result->getTopic());
        $I->assertEquals($payload, $result->getPayload());
    }
}