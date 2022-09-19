<?php

namespace App\Domain\Notification;

use App\Domain\Notification\Exception\AliceNotificationException;

final class AliceNotification implements NotificationInterface
{
    /**
     * @throws AliceNotificationException
     */
    public function __construct(private string $message)
    {
        if (mb_strlen($this->message) > 100) {
            throw AliceNotificationException::manyCharacters();
        }
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