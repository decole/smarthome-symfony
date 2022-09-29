<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\Alert\AlertService;
use App\Application\Service\DeviceData\DeviceCacheService;
use App\Application\Service\DeviceData\DeviceDataCacheService;
use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use App\Domain\Doctrine\Relay\Entity\Relay;
use Cron\CronExpression;

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
        private AlertService $alertService
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

        $this->alertService->messengerNotify($message);
        $this->alertService->aliceNotify($message);
    }
}