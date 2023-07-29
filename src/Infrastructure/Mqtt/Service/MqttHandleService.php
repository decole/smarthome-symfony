<?php

declare(strict_types=1);

namespace App\Infrastructure\Mqtt\Service;

use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Entity\MqttClientInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class MqttHandleService
{
    public function __construct(
        private readonly MqttClientInterface $client,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger
    ) {
    }

    public function post(DevicePayload $message): void
    {
        try {
            $this->client->publish($message->getTopic(), $message->getPayload());
        } catch (Throwable $exception) {
            $text = 'Crash public payload from mqtt protocol';

            $this->logger->critical($text, [
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent($text, [AlertNotificationEvent::MESSENGER]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }
    }
}