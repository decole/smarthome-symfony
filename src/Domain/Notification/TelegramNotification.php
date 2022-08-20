<?php

namespace App\Domain\Notification;

final class TelegramNotification implements NotificationInterface
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