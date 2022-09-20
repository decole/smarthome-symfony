<?php

namespace App\Domain\Notification;

use App\Domain\Notification\Exception\AliceNotificationException;

final class AliceNotification implements NotificationInterface
{
    public function __construct(private string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @throws AliceNotificationException
     */
    public function getTo(): void
    {
        throw AliceNotificationException::notUsingMethod();
    }
}