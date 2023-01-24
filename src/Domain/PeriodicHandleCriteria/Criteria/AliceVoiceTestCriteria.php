<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Cron\CronExpression;

/**
 * Необходимость периодически тестировать работоспособность оповещения через колонку
 */
final class AliceVoiceTestCriteria implements PeriodicHandleCriteriaInterface
{
    public function __construct(private readonly QuasarNotificationService $service)
    {
    }

    public static function alias(): string
    {
        return 'voice_test';
    }

    public function isDue(): bool
    {
        return (new CronExpression('29 8 * * *'))->isDue();
    }

    public function execute(): void
    {
        $this->service->send('Доброе утро');
    }
}