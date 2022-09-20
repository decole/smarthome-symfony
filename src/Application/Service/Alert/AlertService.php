<?php

namespace App\Application\Service\Alert;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Domain\Notification\Event\NotificationEvent;
use App\Domain\Notification\TelegramNotification;
use Psr\EventDispatcher\EventDispatcherInterface;

final class AlertService
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function userNotify(User $user, string $message): void
    {
        $event = new NotificationEvent(new TelegramNotification($user->getTelegramId(), $message));
        $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);
    }

    /**
     * @param EntityInterface $device
     * @param string $payload
     * @return string
     */
    public function prepareMessage(EntityInterface $device, string $payload): string
    {
        /** @var Sensor|Relay|Security|FireSecurity $device */
        $deviceAlertMessage = $device?->getStatusMessage()?->getMessageWarn();

        if ($deviceAlertMessage === null) {
            return sprintf("Внимание! {$device->getName()} имеет состояние: %s", $payload);
        }

        $search = [
            '{value}',
            '%s'
        ];

        return str_replace($search, $payload, $deviceAlertMessage);
    }
}