<?php

declare(strict_types=1);

namespace App\Infrastructure\Event\Listener;

use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Notification\Service\NotifyService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: AlertNotificationEvent::NAME, method: 'onAlertSend')]
final class AlertNotificationEventListener
{
    public function __construct(private readonly NotifyService $alertService)
    {
    }

    public function onAlertSend(AlertNotificationEvent $event): void
    {
        foreach ($event->getTypes() as $type) {
            match ($type) {
                $event::MESSENGER => $this->alertService->messengerNotify($event->getMessage()),
                $event::DISCORD => $this->alertService->discordNotify($event->getMessage()),
                $event::ALICE => $this->alertService->aliceNotify($event->getMessage()),
            };
        }
    }
}