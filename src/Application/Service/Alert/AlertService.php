<?php

namespace App\Application\Service\Alert;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Domain\Notification\AliceNotificationMessage;
use App\Domain\Notification\DiscordNotificationMessage;
use App\Domain\Notification\Event\NotificationEvent;
use App\Domain\Notification\TelegramNotificationMessage;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Сервис, через который происходит алертинг проекта
 */
final class AlertService
{
    public function __construct(private EventDispatcherInterface $eventDispatcher, private UserRepository $repository)
    {
    }

    /**
     * Нотификация в телеграм и дискорд
     *
     * @param string $message
     * @return void
     */
    public function messengerNotify(string $message): void
    {
        foreach ($this->repository->findAllWithTelegramId() as $user) {
            $event = new NotificationEvent(new TelegramNotificationMessage($user->getTelegramId(), $message));
            $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);
        }

        $event = new NotificationEvent(new DiscordNotificationMessage($message));
        $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);
    }

    /**
     * Нотификация через колонку с Алисой по сервису Quasar IOT
     *
     * @param string $message
     * @return void
     */
    public function aliceNotify(string $message): void
    {
        $event = new NotificationEvent(new AliceNotificationMessage($message));
        $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);
    }

    /**
     * @param EntityInterface $device
     * @param string $payload
     * @return string
     */
    public function prepareDeviceAlert(EntityInterface $device, string $payload): string
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