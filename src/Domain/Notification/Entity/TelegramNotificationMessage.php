<?php

namespace App\Domain\Notification\Entity;

final class TelegramNotificationMessage implements NotificationMessageInterface, NotificationUserInterface
{
    public function __construct(private readonly string $message, private readonly int $userId)
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