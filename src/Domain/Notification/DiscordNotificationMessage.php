<?php

namespace App\Domain\Notification;

final class DiscordNotificationMessage implements NotificationMessageInterface
{
    public function __construct(private string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}