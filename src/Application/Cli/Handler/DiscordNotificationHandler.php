<?php

namespace App\Application\Cli\Handler;

use App\Domain\Notification\DiscordNotificationMessage;
use App\Infrastructure\Discord\Service\DiscordService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DiscordNotificationHandler
{
    public function __construct(private DiscordService $service)
    {
    }

    public function __invoke(DiscordNotificationMessage $message): void
    {
        $this->service->send($message->getMessage());
    }
}