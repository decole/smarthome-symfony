<?php

namespace App\Infrastructure\Event\Listener;

use App\Application\Service\Alert\AlertService;
use App\Domain\Event\AlertNotificationEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: AlertNotificationEvent::NAME, method: 'onAlertSend')]
class AlertNotificationEventListener
{
    public function __construct(private AlertService $alertService)
    {
    }

    public function onAlertSend(AlertNotificationEvent $event): void
    {
        foreach ($event->getTypes() as $type) {
            match ($type) {
                $event::MESSENGER => $this->alertService->messengerNotify($event->getMessage()),
                $event::ALICE => $this->alertService->aliceNotify($event->getMessage()),
            };
        }
    }
}