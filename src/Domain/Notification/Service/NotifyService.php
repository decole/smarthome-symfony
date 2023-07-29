<?php

declare(strict_types=1);

namespace App\Domain\Notification\Service;

use App\Domain\Event\NotificationEvent;
use App\Domain\Notification\Entity\AliceNotificationMessage;
use App\Domain\Notification\Entity\DiscordNotificationMessage;
use App\Domain\Notification\Entity\TelegramNotificationMessage;
use App\Domain\VisualNotification\Dto\VisualNotificationDto;
use App\Domain\VisualNotification\Service\VisualNotificationService;
use App\Infrastructure\Repository\Identity\UserRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Сервис, через который происходит алертинг проекта
 */
final class NotifyService
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly VisualNotificationService $service,
        private readonly UserRepository $repository
    ) {
    }

    /**
     * Нотификация в телеграм (и в дискорд, если телеграм сообщений нет)
     *
     * @param string $message
     * @return void
     */
    public function messengerNotify(string $message): void
    {
        $count = 0;

        foreach ($this->repository->findAllWithTelegramId() as $user) {
            $id = $user->getTelegramId();

            if ($id === '' || $id === null) {
                return;
            }

            $event = new NotificationEvent(new TelegramNotificationMessage($message, $user->getTelegramId()));
            $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);

            $count++;
        }

        if ($count === 0) {
            $event = new NotificationEvent(new DiscordNotificationMessage($message));
            $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);
        }
    }

    /**
     * Нотификация в дискорд
     *
     * @param string $message
     * @return void
     */
    public function discordNotify(string $message): void
    {
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

    public function visualNotify(string $message, int $type): void
    {
        $this->service->save(new VisualNotificationDto($type, $message));
    }
}