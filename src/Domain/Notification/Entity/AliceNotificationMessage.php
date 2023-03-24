<?php

namespace App\Domain\Notification\Entity;

final class AliceNotificationMessage implements NotificationMessageInterface
{
    public function __construct(private readonly string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}