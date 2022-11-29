<?php

namespace App\Infrastructure\Mqtt\Entity;

interface MqttClientInterface
{
    public static function getInstance(): MqttClientInterface;

    public function setCredentials(string $broker, int $port): void;

    public function getClient(): mixed;

    public function isConnect(): bool;

    public function setIsConnect(bool $state): void;

    public function connect(): void;

    public function disconnect(): void;

    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false): void;

    public function subscribe(string $topic, int $qos): void;

    public function onMessage(mixed $callback): void;

    public function loop(): void;
}