<?php

namespace App\Domain\PLC\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;

class PLC implements EntityInterface
{
    use Entity, CreatedAt, UpdatedAt;

    public const STATUS_WARNING = 2;
    public const STATUS_ACTIVE = 1;
    public const STATUS_DEACTIVATE = 0;

    public const STATUS_MAP = [
        self::STATUS_ACTIVE,
        self::STATUS_DEACTIVATE,
        self::STATUS_WARNING,
    ];

    public function __construct(
        private string $name,
        private string $targetTopic,
        private int $alarmSecondDelay,

        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify
    ) {
        $this->identify();
        $this->onCreated();
    }

    public static function alias(): string
    {
        return 'plc';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTargetTopic(): string
    {
        return $this->targetTopic;
    }

    public function setTargetTopic(string $targetTopic): void
    {
        $this->targetTopic = $targetTopic;
    }

    public function getAlarmSecondDelay(): int
    {
        return $this->alarmSecondDelay;
    }

    public function setAlarmSecondDelay(int $alarmSecondDelay): void
    {
        $this->alarmSecondDelay = $alarmSecondDelay;
    }

    public function getStatusMessage(): StatusMessage
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(StatusMessage $statusMessage): void
    {
        $this->statusMessage = $statusMessage;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function isNotify(): bool
    {
        return $this->notify;
    }

    public function setNotify(bool $notify): void
    {
        $this->notify = $notify;
    }
}