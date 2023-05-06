<?php

namespace App\Domain\VisualNotification\Service;

use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Contract\Repository\VisualNotificationRepositoryInterface;
use App\Domain\VisualNotification\Dto\VisualNotificationDto;
use App\Domain\VisualNotification\Entity\VisualNotification;
use App\Infrastructure\Cache\CacheService;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

final class VisualNotificationService
{
    private const CACHE_KEY_ALL_TYPE = 'visual.notification.all';
    private const CACHE_KEY_MESSAGE_TYPE = 'visual.notification.notify';
    private const CACHE_KEY_ALERT_TYPE = 'visual.notification.alert';
    private const FIRE_SECURE_TYPE = 'visual.notification.fire.secure';
    private const CACHE_KEY_SECURITY_TYPE = 'visual.notification.security';

    private const MAP = [
        self::CACHE_KEY_ALL_TYPE => null,
        self::CACHE_KEY_MESSAGE_TYPE => VisualNotification::MESSAGE_TYPE,
        self::CACHE_KEY_ALERT_TYPE => VisualNotification::ALERT_TYPE,
        self::FIRE_SECURE_TYPE => VisualNotification::FIRE_SECURE_TYPE,
        self::CACHE_KEY_SECURITY_TYPE => VisualNotification::SECURITY_TYPE,
    ];

    public function __construct(
        private readonly TransactionInterface $transaction,
        private readonly VisualNotificationRepositoryInterface $repository,
        private readonly CacheService $cacheService
    ) {
    }

    /**
     * twig global var service. See /config/packages/twig.yaml
     * Визуальные нотификации - общие нотификации. Не будет филдьтров по пользователю.
     * Данные кэшируются
     * @return array<string, mixed>
     */
    public function notifications(): array
    {
        $notifies = $this->getNotifiesByType();

        $total = $alerts = $notifications = $fireSecureAlerts = $secureAlerts = 0;
        $alertsTime = $notificationsTime = $fireSecureAlertsTime = $secureAlertsTime = null;

        foreach ($notifies as $notify) {
            if ($notify->getType() === VisualNotification::MESSAGE_TYPE) {
                $notifications++;
                $notificationsTime = $this->minimalTime($notificationsTime, $notify->getCreatedAt());
            }

            if ($notify->getType() === VisualNotification::ALERT_TYPE) {
                $alerts++;
                $alertsTime = $this->minimalTime($alertsTime, $notify->getCreatedAt());
            }

            if ($notify->getType() === VisualNotification::FIRE_SECURE_TYPE) {
                $fireSecureAlerts++;
                $fireSecureAlertsTime = $this->minimalTime($fireSecureAlertsTime, $notify->getCreatedAt());
            }

            if ($notify->getType() === VisualNotification::SECURITY_TYPE) {
                $secureAlerts++;
                $secureAlertsTime = $this->minimalTime($secureAlertsTime, $notify->getCreatedAt());
            }

            $total++;
        }

        return [
            'total' => $total,
            'alerts' => [
                'count' => $alerts,
                'difTime' => $this->diffTime($alertsTime),
            ],
            'notifications' => [
                'count' => $notifications,
                'difTime' => $this->diffTime($notificationsTime),
            ],
            'securities' => [
                'fireSecureAlerts' => [
                    'count' => $fireSecureAlerts,
                    'difTime' => $this->diffTime($fireSecureAlertsTime),
                ],
                'secureAlerts' => [
                    'count' => $secureAlerts,
                    'difTime' => $this->diffTime($secureAlertsTime),
                ],
            ],
        ];
    }

    public function save(VisualNotificationDto $dto): void
    {
        $entity = new VisualNotification($dto->getType(), $dto->getMessage());

        $this->transaction->transactional(
            fn () => $this->repository->save($entity)
        );

        $this->refreshCache($dto->getType());
    }

    public function setIsRead(?int $type = null): void
    {
        $this->transaction->transactional(
            fn () => $this->repository->setAllIsRead($type)
        );

        $this->refreshCache($type);
    }

    /**
     * @param int|null $type
     * @return array<int, VisualNotification>
     * @throws InvalidArgumentException
     */
    public function getNotifiesByType(?int $type = null): array
    {
        $cacheKey = self::CACHE_KEY_ALL_TYPE;

        foreach (self::MAP as $mapKey => $mapType) {
            if ($mapType === $type) {
                $cacheKey = $mapKey;
            }
        }

        return $this->cacheService->getOrSet(
            key: $cacheKey,
            callback: function (ItemInterface $item) use ($type) {
                $item->expiresAfter(3600);
                return $this->repository->findByTypeAndIsRead(type: $type, isRead: false);
            }
        );
    }

    public function refreshCache(?int $notifyType): void
    {
        if ($notifyType === null) {
            foreach (self::MAP as $key => $type) {
                $this->cacheService->set($key, $this->repository->findByTypeAndIsRead($type, false));
            }

            return;
        }

        $this->cacheService->set(self::CACHE_KEY_ALL_TYPE, $this->repository->findByTypeAndIsRead(null, false));

        foreach (self::MAP as $key => $type) {
            if ($notifyType === $type) {
                $this->cacheService->set($key, $this->repository->findByTypeAndIsRead($type, false));
            }
        }
    }

    private function minimalTime(?DateTimeImmutable $time, DateTimeImmutable $createdAt): ?DateTimeImmutable
    {
        if ($time === null) {
            return $createdAt;
        }

        if ($time > $createdAt) {
            return $createdAt;
        }

        return $time;
    }

    private function diffTime(?DateTimeImmutable $time): ?string
    {
        if ($time === null) {
            return null;
        }

        $current = new DateTimeImmutable('now', new DateTimeZone('utc'));

        $diff = $current->getTimestamp() - $time->getTimestamp();

        if ($diff < 60) {
            return "{$diff} seconds";
        }

        if ($diff < 3600) {
            return ceil($diff/60) . ' minutes';
        }

        if ($diff < 86400) {
            return ceil($diff/3600) . ' hours';
        }

        return ceil($diff/86400) . ' days';
    }
}