<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\DeviceData\DeviceDataCacheService;
use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;

/**
 * фоновый процесс, который периоджически перебирает хранящиеся данные датчиков и удаляет просроченные
 */
final class DeviceClearOldDataCriteria implements PeriodicHandleCriteriaInterface
{
    public function __construct(private DeviceDataCacheService $service)
    {
    }

    public static function alias(): string
    {
        return 'clear old data by smart home device';
    }

    public function isDue(): bool
    {
        return true;
    }

    public function execute(): void
    {
        $this->service->clearOldPayload();
    }
}