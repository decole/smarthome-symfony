<?php

namespace App\Infrastructure\Mqtt\Service;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Entity\MqttClientInterface;
use App\Tests\Stub\Infrastructure\StubMqttClient;
use Mosquitto\Client;
use Mosquitto\Message;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class MqttHandleService
{
    // для тестов тип mixed, ибо используется стабнутый инстанс MqttClientInterface со своим клиентом-заглушкой
    private mixed $client;

    public function __construct(
        private MqttClientInterface $instance,
        private DeviceDataResolver $resolver,
        private DeviceCacheService $deviceCacheService,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
        private string $broker,
        private string $port
    ) {
        /** @var MqttClientInterface $client*/
        $this->client = $this->instance::getInstance();
        $this->client->setCredentials($this->broker, $this->port);
    }

    public function process(Message $message): void
    {
        try {
            $payload = $this->createPayload($message);
            $this->resolver->resolveDevicePayload($payload);
        } catch (Throwable $exception) {
            $this->logger->info('Error mqtt listen process', [
                'topic' => $message->topic,
                'payload' => $message->payload,
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent('Ошибка в распознавании данных из брокера сообщений', [
                AlertNotificationEvent::MESSENGER,
                AlertNotificationEvent::ALICE
            ]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);

            $event = new AlertNotificationEvent(
                "topic: [{$message->topic}], payload: [{$message->payload}] | {$exception->getMessage()}",
                [AlertNotificationEvent::MESSENGER]
            );
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }
    }

    public function post(DevicePayload $message): void
    {
        try {
            $this->client->publish($message->getTopic(), $message->getPayload(), 1);
        } catch (Throwable $exception) {
            $this->logger->critical('Crash api post command to mqtt protocol', [
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent('Не возможно отправить api команду брокеру сообщений', [
                AlertNotificationEvent::MESSENGER,
                AlertNotificationEvent::ALICE
            ]);
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
        if ($this->instance instanceof StubMqttClient) {
            return;
        }

        while (true) {
            $this->client->loop();
        }
    }
}