<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use Cron\CronExpression;

/**
 * Оповестить о начале текущего события по календарю через алису и дискорд
 */
final class CalendarCriteria implements PeriodicHandleCriteriaInterface
{
    public static function alias(): string
    {
        return 'calendar';
    }

    public function isDue(): bool
    {
        // https://github.com/dragonmantank/cron-expression
        return (new CronExpression('* * * * *'))->isDue();
    }

    public function execute(): void
    {
        // https://github.com/spatie/laravel-google-calendar
    }
}