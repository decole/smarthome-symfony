<?php

namespace App\Domain\PLC\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;

final class PLC implements EntityInterface
{
    use Entity, CreatedAt, UpdatedAt;

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

        $this->checkStatusType($status);
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
        $this->checkStatusType($status);

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

    /**
     * @throws UnresolvableArgumentException
     */
    private function checkStatusType(?int $status): void
    {
        if (EntityStatusEnum::tryFrom($status) === null) {
            throw UnresolvableArgumentException::argumentIsNotSet('PLC status');
        }
    }
}