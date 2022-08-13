<?php


namespace App\Infrastructure\Mqtt\Service;


use App\Domain\Payload\Dto\MessageDto;
use Psr\Log\LoggerInterface;
use Mosquitto\Client;
use Mosquitto\Message;

class MqttHandleService
{
    private const CACHE_LIMIT = 120;

    private const CACHE_TOPIC_LIST = 'mqtt_topic_list';

    private bool $isConnect = false;

    private Client $client;

    public function __construct(
        private MqttService $service,
        private LoggerInterface $logger,
        private string $broker,
        private string $port
    ) {
        $this->client = new Client();
    }

    public function listen(): void
    {
        $this->connectClient();
        $this->registerClient();

        while (true) {
            $this->client->loop(2);
        }
    }

    public function process(Message $message): void
    {
        $this->service->route($message);

//        $this->logger->info('mqtt message', [
//            'topic' => $message->topic,
//            'payload' => $message->payload,
//        ]);
    }

    public function post(MessageDto $message): void
    {
        $this->client->publish($message->getTopic(), $message->getPayload(), 1, 0);
    }

    private function connectClient(): void
    {
        $this->client->connect($this->broker, $this->port, 5);
    }

    public function disconnect(): void
    {
        if ($this->isConnect) {
            $this->client->disconnect();
        }
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
}