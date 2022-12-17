<?php

namespace App\Infrastructure\Discord\Service;

use App\Domain\Event\AlertNotificationEvent;
use App\Infrastructure\Cache\CacheKeyListEnum;
use App\Infrastructure\Cache\CacheService;
use App\Infrastructure\Discord\Exception\DiscordServiceNullWebhookException;
use GuzzleHttp\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class DiscordService
{
    private Client $client;

    public function __construct(
        private CacheService $cache,
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher,
        private ?string $webhookUri = null
    ) {
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
            $sentMessage = $this->cache->get(key: CacheKeyListEnum::DISCORD_SENT_MESSAGE_TRIGGER);

            if ($sentMessage === $message) {
                return;
            }

            $this->cache->set(
                key: CacheKeyListEnum::DISCORD_SENT_MESSAGE_TRIGGER,
                value: $message,
                lifetime: 60
            );

            $this->client->post($this->webhookUri, [
                'json' => [
                    'content' => $message,
                ],
            ]);
        } catch (Throwable $exception) {
            $this->logger->critical('Can`t send discord message', [
                'exception' => $exception->getMessage(),
            ]);

            $this->eventDispatcher->dispatch(
                new AlertNotificationEvent(
                    $exception->getMessage(),
                    [AlertNotificationEvent::MESSENGER]
                ),
                AlertNotificationEvent::NAME
            );
        }
    }
}