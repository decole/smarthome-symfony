<?php

namespace App\Infrastructure\Mqtt\Service;

use App\Application\Service\DeviceData\DataResolver;
use App\Application\Service\DeviceData\DeviceCacheService;
use App\Domain\Payload\DevicePayload;
use Mosquitto\Client;
use Mosquitto\Message;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @see https://mosquitto-php.readthedocs.io/en/latest/client.html#Mosquitto\Client::onConnect
 */
final class MqttHandleService
{
    private bool $isConnect = false;

    private static ?Client $client = null;

    public function __construct(
        private DeviceCacheService $deviceCacheService,
        private DataResolver $resolver,
        private LoggerInterface $logger,
        private string $broker,
        private string $port
    ) {
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
        $this->getClient()->publish($message->getTopic(), $message->getPayload(), 1, 0);
    }

    public function listen(): void
    {
        $this->deviceCacheService->create();
        $this->connectClient();
        $this->registerClient();

        while (true) {
            $this->getClient()->loop(2);
        }
    }

    /**
     * Сделано так - статичная переменная, чтобы был только один коннект в брокеру и быстро работала отправка сообщений
     *
     * @return Client
     */
    private function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client();
            $this->getClient()->connect($this->broker, $this->port, 5);
        }

        return self::$client;
    }

    public function disconnect(): void
    {
        if ($this->isConnect) {
            $this->getClient()->disconnect();
        }
    }

    private function connectClient(): void
    {
        $this->getClient()->connect($this->broker, $this->port, 5);
    }

    private function registerClient(): void
    {
        $this->getClient()->onConnect(function ($rc) {
            $this->isConnect = $rc === 0;
        });

        $this->getClient()->onDisconnect(function () {
            $this->isConnect = false;
            sleep(60);
        });

        $this->getClient()->subscribe('#', 1);
        $this->getClient()->onMessage([$this, 'process']);

        register_shutdown_function([$this, 'disconnect']);
    }

    private function createPayload(Message $messageMqtt): DevicePayload
    {
        return (new DevicePayload(topic: $messageMqtt->topic, payload: $messageMqtt->payload));
    }
}