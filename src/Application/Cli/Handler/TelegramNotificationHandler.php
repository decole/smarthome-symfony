<?php

namespace App\Application\Cli\Handler;

use App\Domain\Notification\Entity\TelegramNotificationMessage;
use App\Infrastructure\Telegram\Service\TelegramService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TelegramNotificationHandler
{
    public function __construct(private readonly TelegramService $service)
    {
    }

    public function __invoke(TelegramNotificationMessage $message): void
    {
        $this->service->send($message->getTo(), $message->getMessage());

        sleep(1);
    }
}