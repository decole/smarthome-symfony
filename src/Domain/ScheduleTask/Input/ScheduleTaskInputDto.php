<?php

namespace App\Domain\ScheduleTask\Input;

use DateTimeImmutable;

class ScheduleTaskInputDto
{
    public function __construct(
        public readonly string $command,
        public readonly array $arguments,
        public readonly ?string $interval,
        public readonly ?DateTimeImmutable $nextRun
    ) {
    }
}