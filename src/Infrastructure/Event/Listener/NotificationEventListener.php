<?php

declare(strict_types=1);

namespace App\Infrastructure\Event\Listener;

use App\Domain\Event\NotificationEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener(event: NotificationEvent::NAME, method: 'onNotificationSend')]
final class NotificationEventListener
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function onNotificationSend(NotificationEvent $event): void
    {
        $this->bus->dispatch($event->getNotify());
    }
}