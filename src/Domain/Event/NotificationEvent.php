<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Notification\Entity\NotificationMessageInterface;
use Symfony\Contracts\EventDispatcher\Event;

class NotificationEvent extends Event
{
    public const NAME = 'notification.send';

    public function __construct(private readonly NotificationMessageInterface $notify)
    {
    }

    public function getNotify(): NotificationMessageInterface
    {
        return $this->notify;
    }
}