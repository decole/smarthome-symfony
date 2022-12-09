<?php

namespace App\Tests\Stub\Infrastructure;

use App\Infrastructure\Mqtt\Entity\MqttClientInterface;

final class StubMqttClient implements MqttClientInterface
{
    private bool $isConnect = false;

    public function getClient(): mixed
    {
        return (object)[];
    }

    public function isConnect(): bool
    {
        return false === $this->isConnect;
    }

    public function setIsConnect(bool $state): void
    {
        $this->isConnect = $state;
    }

    public function disconnect(): void
    {
        $this->isConnect = false;
    }

    public function connect(): void
    {
        $this->isConnect = true;
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