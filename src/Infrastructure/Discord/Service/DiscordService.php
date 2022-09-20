<?php

namespace App\Infrastructure\Discord\Service;

use App\Infrastructure\Discord\Exception\DiscordServiceNullWebhookException;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Throwable;

final class DiscordService
{
    private Client $client;

    public function __construct(private LoggerInterface $logger, private ?string $webhookUri = null)
    {
        if (!$webhookUri) {
            $this->logger->critical('Please configure discord webhook');

            throw DiscordServiceNullWebhookException::nullWebhook();
        }

        $this->client = new Client([
            'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            'allow_redirects' => false,
        ]);
    }

    public function send(string $message): void
    {
        try {
            $this->client->post($this->webhookUri, [
                'json' => [
                    'content' => $message,
                ],
            ]);
        } catch (Throwable $exception) {
            $this->logger->critical('Can`t send discord message', [
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}