<?php

declare(strict_types=1);

namespace App\Infrastructure\Mqtt\Entity;

use App\Infrastructure\Mqtt\Exception\MqttException;
use Closure;
use Mosquitto\Client;
use Mosquitto\Message;

/**
 * @see https://mosquitto-php.readthedocs.io/en/latest/client.html#Mosquitto\Client::onConnect
 */
final class MqttClient implements MqttClientInterface
{
    private const KEEPALIVE = 60;

    private bool $isConnect = false;

    private ?Client $client = null;

    public function __construct(private string $broker, private int $port)
    {
    }

    public function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client();
            register_shutdown_function([$this, 'disconnect']);
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
        if ($this->isConnect()) {
            return;
        }

        $this->getClient()->connect($this->broker, $this->port);
        $this->getClient()->setReconnectDelay(self::KEEPALIVE);
        $this->isConnect = true;

        $this->getClient()->onConnect(fn ($rc) => $this->isConnect = $rc === 0);
        $this->getClient()->onDisconnect(function() {
            $this->isConnect = false;
            $this->disconnect();
        });
    }

    public function disconnect(): void
    {
        $this->isConnect = false;
        $this->client?->disconnect();
    }

    // https://github.com/mgdm/Mosquitto-PHP/blob/php8/tests/Client/publish.phpt
    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false): void
    {
        $client = $this->getClient();
        $this->isConnect = $looping = true;

        $client->onConnect(function() use ($client, $topic, $payload) {
            $client->publish($topic, $payload, 0);
        });

        $client->onMessage(function(Message $message) use ($client, &$looping) {
            $client->disconnect();
            $this->isConnect = $looping = false;
        });

        $client->connect($this->broker, $this->port, self::KEEPALIVE);

        for ($i = 0; $i < 10; $i++) {
            if (!$looping) {
                $this->isConnect = false;
                break;
            }
            $client->loop(50);
        }
    }

    public function subscribe(string $topic, int $qos, Closure $closure): void
    {
        $this->getClient()->subscribe($topic, $qos);
    }
}