<?php

namespace App\Infrastructure\Mqtt\Entity;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataResolver;
use Mosquitto\Client;
use Psr\Log\LoggerInterface;

/**
 * @see https://mosquitto-php.readthedocs.io/en/latest/client.html#Mosquitto\Client::onConnect
 */
final class MqttClient implements MqttClientInterface
{
    private const KEEPALIVE = 10;

    private static bool $isConnect = false;

    private static ?MqttClient $instance = null;

    private ?Client $client = null;

    private string $broker;

    private int $port;

    public static function getInstance(): MqttClientInterface
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setCredentials(string $broker, int $port): void
    {
        $this->broker = $broker;
        $this->port = $port;
    }

    public function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }

    public function isConnect(): bool
    {
        return !self::$isConnect && $this->client !== null;
    }

    public function setIsConnect(bool $state): void
    {
        self::$isConnect = $state;
    }

    public function connect(): void
    {
        if (!$this->isConnect()) {
            $this->getClient()->connect($this->broker, $this->port, self::KEEPALIVE);
            self::$isConnect = true;

            $this->getClient()->onConnect(fn ($rc) => self::$isConnect = $rc === 0);
            $this->getClient()->onDisconnect(function () {
                self::$isConnect = false;
                sleep(60);
            });
        }
    }

    public function disconnect(): void
    {
        self::$isConnect = false;
        $this->client?->disconnect();
    }

    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false): void
    {
        $this->connect();
        $this->getClient()->publish($topic, $payload, $qos, $retain);
    }

    public function subscribe(string $topic, int $qos): void
    {
        $this->getClient()->subscribe($topic, $qos);
    }

    public function onMessage(mixed $callback): void
    {
        $this->getClient()->onMessage($callback);
    }

    public function loop(): void
    {
        $this->getClient()->loop();
    }
}