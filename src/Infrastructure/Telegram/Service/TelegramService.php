<?php

namespace App\Infrastructure\Telegram\Service;

use App\Infrastructure\Telegram\Exception\TelegramServiceException;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;

final class TelegramService
{
    private Api $telegram;

    /**
     * @throws TelegramServiceException
     * @throws TelegramSDKException
     */
    public function __construct(private LoggerInterface $logger, private ?string $apiToken = null)
    {
        if ($this->apiToken === null) {
            $this->logger->error('Telegram bot not configured, Api token is null');

            throw TelegramServiceException::apiTokenEmpty();
        }

        $this->telegram = new Api($apiToken);
    }

    public function sendMessage(string $chatId, string $notify): void
    {
        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $notify,
            ]);
        } catch (Throwable $exception) {
            $this->logger->critical('Can`t send telegram message', [
                'exception' => $exception->getMessage(),
                'message' => $notify,
                'chatId' => $chatId,
            ]);
        }
    }
}