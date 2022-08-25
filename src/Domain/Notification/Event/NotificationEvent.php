<?php

namespace App\Domain\Notification\Event;

use App\Domain\Notification\NotificationInterface;
use Symfony\Contracts\EventDispatcher\Event;

class NotificationEvent extends Event
{
    public const NAME = 'notification.send';

    public function __construct(private NotificationInterface $notify)
    {
    }

    public function getNotify(): NotificationInterface
    {
        return $this->notify;
    }
}