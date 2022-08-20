<?php


namespace App\Application\Cli\Handler;


use App\Domain\Notification\TelegramNotification;
use App\Infrastructure\Telegram\Service\TelegramService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TelegramNotificationHandler
{
    public function __construct(private TelegramService $service)
    {
    }

    public function __invoke(TelegramNotification $message): void
    {
        $this->service->sendMessage($message->getTo(), $message->getMessage());
    }
}