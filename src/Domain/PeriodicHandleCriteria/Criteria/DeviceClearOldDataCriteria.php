<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\DeviceData\DeviceDataCacheService;
use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use Cron\CronExpression;

/**
 * фоновый процесс, который раз в минуту перебирает хранящиеся данные датчиков и удаляет просроченные
 */
final class DeviceClearOldDataCriteria implements PeriodicHandleCriteriaInterface
{
    public function __construct(private DeviceDataCacheService $service)
    {
    }

    public static function alias(): string
    {
        return 'clear device old data';
    }

    public function isDue(): bool
    {
        return (new CronExpression('* * * * *'))->isDue();
    }

    public function execute(): void
    {
        $this->service->clearOldPayload();
    }
}