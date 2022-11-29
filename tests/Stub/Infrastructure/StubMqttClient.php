<?php

namespace App\Tests\Stub\Infrastructure;

use App\Infrastructure\Mqtt\Entity\MqttClientInterface;

class StubMqttClient implements MqttClientInterface
{
    private static bool $isConnect = false;

    private static ?StubMqttClient $instance = null;

    private string $broker;

    private int $port;

    public static function getInstance(): MqttClientInterface
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getClient(): mixed
    {
        return (object)[];
    }

    public function isConnect(): bool
    {
        return false === self::$isConnect;
    }

    public function setIsConnect(bool $state): void
    {
        self::$isConnect = $state;
    }

    public function disconnect(): void
    {
        self::$isConnect = false;
    }

    public function connect(): void
    {
        self::$isConnect = true;
    }

    public function setCredentials(string $broker, int $port): void
    {
        $this->broker = $broker;
        $this->port = $port;
    }

    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false): void
    {
    }

    public function subscribe(string $topic, int $qos): void
    {
    }

    public function onMessage(mixed $callback): void
    {
    }

    public function loop(): void
    {
    }
}