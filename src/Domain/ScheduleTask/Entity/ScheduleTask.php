<?php

namespace App\Domain\ScheduleTask\Entity;

use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;
use DateTimeImmutable;

final class ScheduleTask implements EntityInterface
{
    use Entity, CreatedAt, UpdatedAt;

    public function __construct(
        private string $command,
        private array $arguments,
        private ?string $interval,
        private ?DateTimeImmutable $lastRun,
        private ?DateTimeImmutable $nextRun
    ) {
        $this->identify();
        $this->onCreated();
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command): void
    {
        $this->command = $command;
        $this->onUpdated();
    }

    public function getInterval(): ?string
    {
        return $this->interval;
    }

    // интервал может быть как DateInterval строка, так и cron строка
    public function setInterval(?string $interval): void
    {
        $this->interval = $interval;
        $this->onUpdated();
    }

    public function getLastRun(): ?DateTimeImmutable
    {
        return $this->lastRun;
    }

    public function setLastRun(): void
    {
        $this->lastRun = new DateTimeImmutable();
    }

    public function getNextRun(): ?DateTimeImmutable
    {
        return $this->nextRun;
    }

    public function setNextRun(?DateTimeImmutable $nextRun): void
    {
        if ($nextRun !== null) {
            /** @var DateTimeImmutable $nextRun */
            [$hour, $minute, $second] = [$nextRun->format('H'), $nextRun->format('i'), 0];

            $nextRun = $nextRun->setTime(hour: $hour, minute: $minute, second: $second);
        }

        $this->nextRun = $nextRun;
        $this->onUpdated();
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
        $this->onUpdated();
    }

    public static function alias(): string
    {
        return 'background_task';
    }
}