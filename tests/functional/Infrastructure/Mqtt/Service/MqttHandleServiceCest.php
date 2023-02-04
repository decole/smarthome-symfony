<?php

namespace App\Tests\functional\Infrastructure\Mqtt\Service;

use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Tests\FunctionalTester;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use Codeception\Stub;
use Codeception\Stub\Expected;
use DG\BypassFinals;
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
            eventDispatcher: Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]),
            logger: $logger
        );

        $topic = $I->faker()->word();
        $payload = $I->faker()->word();

        $dto = new DevicePayload(
            topic: $topic,
            payload: $payload
        );

        $service->post($dto);
    }
}