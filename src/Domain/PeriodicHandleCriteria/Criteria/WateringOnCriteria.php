<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\DeviceData\DeviceCacheService;
use App\Application\Service\DeviceData\DeviceDataCacheService;
use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Notification\AliceNotificationMessage;
use App\Domain\Notification\DiscordNotificationMessage;
use App\Domain\Notification\Event\NotificationEvent;
use App\Domain\Notification\TelegramNotificationMessage;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
use Cron\CronExpression;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Оповестить когда центральный клапан открыт
 * нужно для вторичного мониторинга, чтобы не допустить ошибочной траты ресурсов воды
 * оповестить через алису, телеграм и дискорд
 */
final class WateringOnCriteria implements PeriodicHandleCriteriaInterface
{
    public function __construct(
        private DeviceDataCacheService $service,
        private DeviceCacheService $cacheService,
        private UserRepository $repository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public static function alias(): string
    {
        return 'watering major switch';
    }

    private const TOPIC = 'water/check/major';

    public function isDue(): bool
    {
        return (new CronExpression('* * * * *'))->isDue();
    }

    public function execute(): void
    {
        $payloadCheckOn = 1;

        $devices = $this->cacheService->getTopicMapByDeviceType();

        foreach ($devices as $device) {
            if ($device instanceof Relay && $device->getCheckTopic() === self::TOPIC) {
                $payloadCheckOn = $device->getCheckTopicPayloadOn();
            }
        }

        $payloadMap = $this->service->getPayloadByTopicList([self::TOPIC]);

        if ($payloadMap[self::TOPIC] === $payloadCheckOn) {
            $this->notify();
        }
    }

    private function notify(): void
    {
        $message = 'Главный клапан автополива включен';

        $list = [
            new NotificationEvent(new AliceNotificationMessage($message)),
            new NotificationEvent(new DiscordNotificationMessage($message)),
        ];

        $userNotify = [];
        /** @var User $user */
        foreach ($this->repository->findAll() as $user) {
            $telegramId = $user->getTelegramId();
            if ($telegramId !== null) {
                $userNotify[] = new NotificationEvent(new TelegramNotificationMessage($telegramId, $message));
            }
        }

        if (!empty($userNotify)) {
            $list = array_merge($list, $userNotify);
        }

        foreach ($list as $event) {
            $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);
        }
    }
}