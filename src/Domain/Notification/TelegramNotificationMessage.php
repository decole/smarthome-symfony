<?php

namespace App\Domain\Notification;

final class TelegramNotificationMessage implements NotificationMessageInterface, NotificationUserInterface
{
    public function __construct(private int $userId, private string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTo(): int
    {
        return $this->userId;
    }
}