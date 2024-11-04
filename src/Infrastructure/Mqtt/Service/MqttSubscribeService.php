<?php

namespace App\Infrastructure\Mqtt\Service;

use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Entity\MqttClientInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class MqttSubscribeService
{
    private const SUBSCRIBE_TOPIC = '#';

    public function __construct(
        private readonly MqttClientInterface $client,
        private readonly DeviceDataResolver $resolver,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        try {
            $this->client->subscribe(
                self::SUBSCRIBE_TOPIC,
                1,
                function ($topic, $message, $retained, $matchedWildcards): void {
                    $this->resolver->resolveDevicePayload(new DevicePayload($topic, $message));
                }
            );
        } catch (Throwable $exception) {
            $text = 'Crash subscribe to mqtt broker';

            $this->logger->critical($text, [
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent($text, [AlertNotificationEvent::MESSENGER]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }
    }
}