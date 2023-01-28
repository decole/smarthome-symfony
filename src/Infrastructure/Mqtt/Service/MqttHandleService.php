<?php

namespace App\Infrastructure\Mqtt\Service;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Entity\MqttClientInterface;
use App\Infrastructure\Mqtt\Exception\MqttException;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use Mosquitto\Message;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class MqttHandleService
{
    public function __construct(
        private readonly MqttClientInterface $client,
        private readonly DeviceCacheService $deviceCacheService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SerializerInterface $serializer,
        private readonly Producer $receiveProducer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function process(Message $message): void
    {
        try {
            $json = $this->serializer->serialize($this->createPayload($message), 'json');
            $this->receiveProducer->publish($json);
        } catch (Throwable $exception) {
            $text = 'Error mqtt listen process';

            $this->logger->info($text, [
                'topic' => $message->topic,
                'payload' => $message->payload,
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent(
                message: $text . " {$exception->getMessage()}",
                types: [AlertNotificationEvent::MESSENGER]
            );
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);

            $this->client->disconnect();

            throw MqttException::disconnect();
        }
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

    public function listen(): void
    {
        try {
            $this->deviceCacheService->create();
            $this->registerClient();
            $this->demonize();

        } catch (Throwable $exception) {
            $this->logger->critical('Crash MQTT listener', [
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent('Не возможно соединиться с брокером сообщений', [
                AlertNotificationEvent::MESSENGER,
                AlertNotificationEvent::ALICE
            ]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }
    }

    public function disconnect(): void
    {
        $this->client->disconnect();
    }

    private function registerClient(): void
    {
        $this->client->connect();
        $this->client->subscribe('#', 1);
        $this->client->onMessage([$this, 'process']);

        register_shutdown_function([$this, 'disconnect']);
    }

    private function createPayload(Message $messageMqtt): DevicePayload
    {
        return new DevicePayload(topic: $messageMqtt->topic, payload: $messageMqtt->payload);
    }

    private function demonize(): void
    {
        if ($this->client instanceof StubMqttClient) {
            return;
        }

        while (true) {
            $this->client->loop();

            if (!$this->client->isConnect()) {
                throw MqttException::disconnect();
            }
        }
    }
}