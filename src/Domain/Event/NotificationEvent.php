<?php

namespace App\Domain\Event;

use App\Domain\Notification\Entity\NotificationMessageInterface;
use Symfony\Contracts\EventDispatcher\Event;

class NotificationEvent extends Event
{
    public const NAME = 'notification.send';

    public function __construct(private NotificationMessageInterface $notify)
    {
    }

    public function getNotify(): NotificationMessageInterface
    {
        return $this->notify;
    }
}