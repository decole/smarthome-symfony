<?php

namespace App\Infrastructure\Event\Listener;

use App\Domain\Event\VisualNotificationEvent;
use App\Domain\Notification\Service\NotifyService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: VisualNotificationEvent::NAME, method: 'onNotificationSend')]
class VisualNotificationEventListener
{
    public function __construct(private NotifyService $alertService)
    {
    }

    public function onNotificationSend(VisualNotificationEvent $event): void
    {
        $this->alertService->visualNotify($event->getMessage(), $event->getNotifyType());
    }
}