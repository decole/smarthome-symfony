<?php

namespace App\Infrastructure\Event\Listener;

use App\Application\Service\Alert\AlertService;
use App\Domain\Event\VisualNotificationEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: VisualNotificationEvent::NAME, method: 'onNotificationSend')]
class VisualNotificationEventListener
{
    public function __construct(private AlertService $alertService)
    {
    }

    public function onNotificationSend(VisualNotificationEvent $event): void
    {
        $this->alertService->visualNotify($event->getMessage(), $event->getNotifyType());
    }
}