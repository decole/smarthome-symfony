<?php

namespace App\Infrastructure\Mqtt\Entity;

use Closure;
use PhpMqtt\Client\MqttClient as MqttClientAlias;
use Ramsey\Uuid\Uuid;

/**
 * @see https://github.com/php-mqtt/client
 */
final class PhpMqttClient implements MqttClientInterface
{
    private ?MqttClientAlias $client = null;

    private bool $isConnect = false;

    public function __construct(
        private readonly string $broker,
        private readonly int $port
    ) {
    }

    public function getClient(): MqttClientAlias
    {
        if (!$this->client instanceof \PhpMqtt\Client\MqttClient) {
            $this->client = new MqttClientAlias(
                $this->broker,
                $this->port,
                sprintf('php-client-%s', Uuid::uuid4()->toString())
            );
        }

        return $this->client;
    }

    public function isConnect(): bool
    {
        return $this->isConnect;
    }

    public function setIsConnect(bool $state): void
    {
        $this->isConnect = $state;
    }

    public function connect(): void
    {
        $this->getClient()->connect();
    }

    public function disconnect(): void
    {
        $this->getClient()->disconnect();
    }

    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false): void
    {
        $this->connect();
        $this->getClient()->publish($topic, $payload, $qos, $retain);
        $this->disconnect();
    }

    public function subscribe(string $topic, int $qos, Closure $closure): void
    {
        $this->connect();
        $this->getClient()->subscribe($topic, $closure, $qos);
        $this->getClient()->loop();
        $this->disconnect();
    }
}