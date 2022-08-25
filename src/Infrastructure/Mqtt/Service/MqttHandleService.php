<?php

namespace App\Infrastructure\Mqtt\Service;

use App\Application\Service\DeviceData\DataResolver;
use App\Application\Service\DeviceData\DeviceCacheService;
use App\Domain\Payload\DevicePayload;
use Mosquitto\Client;
use Mosquitto\Message;
use Psr\Log\LoggerInterface;
use Throwable;

final class MqttHandleService
{
    private bool $isConnect = false;

    private Client $client;

    public function __construct(
        private DeviceCacheService $deviceCacheService,
        private DataResolver $resolver,
        private LoggerInterface $logger,
        private string $broker,
        private string $port
    ) {
        $this->client = new Client();
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
        }
    }

    public function post(DevicePayload $message): void
    {
        $this->client->publish($message->getTopic(), $message->getPayload(), 1, 0);
    }

    public function listen(): void
    {
        $this->deviceCacheService->create();
        $this->connectClient();
        $this->registerClient();

        while (true) {
            $this->client->loop(2);
        }
    }

    public function disconnect(): void
    {
        if ($this->isConnect) {
            $this->client->disconnect();
        }
    }

    private function connectClient(): void
    {
        $this->client->connect($this->broker, $this->port, 5);
    }

    private function registerClient(): void
    {
        $this->client->onConnect(function ($rc) {
            $this->isConnect = $rc === 0;
        });

        $this->client->onDisconnect(function () {
            $this->isConnect = false;
        });

        $this->client->subscribe('#', 1);
        $this->client->onMessage([$this, 'process']);

        register_shutdown_function([$this, 'disconnect']);
    }

    private function createPayload(Message $messageMqtt): DevicePayload
    {
        return (new DevicePayload(topic: $messageMqtt->topic, payload: $messageMqtt->payload));
    }
}
