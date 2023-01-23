<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Relay\Entity\Relay;
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
        private readonly DeviceDataCacheService $service,
        private readonly DeviceCacheService $cacheService,
        private readonly EventDispatcherInterface $eventDispatcher
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

        $devices = $this->cacheService->getTopicMapByDeviceTopic();

        foreach ($devices as $device) {
            if ($device instanceof Relay && $device->getCheckTopic() === self::TOPIC) {
                $payloadCheckOn = $device->getCheckTopicPayloadOn();
            }
        }

        $payloadMap = $this->service->getPayloadByTopicList([self::TOPIC]);

        if ($payloadMap[self::TOPIC] === $payloadCheckOn) {
            $this->notification();
        }
    }

    private function notification(): void
    {
        $message = 'Главный клапан автополива включен';

        $event = new AlertNotificationEvent($message, [
            AlertNotificationEvent::MESSENGER,
            AlertNotificationEvent::ALICE
        ]);
        $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
    }
}