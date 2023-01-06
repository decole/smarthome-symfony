<?php

namespace App\Domain\FireSecurity\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\CrudCommonFields;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;

final class FireSecurity implements EntityInterface
{
    use Entity, CreatedAt, UpdatedAt, CrudCommonFields;

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,

        private ?string $normalPayload,
        private ?string $alertPayload,
        private ?string $lastCommand,

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
        return 'fireSecurity';
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

    public function getNormalPayload(): ?string
    {
        return $this->normalPayload;
    }

    public function setNormalPayload(?string $normalPayload): void
    {
        $this->normalPayload = $normalPayload;
    }

    public function getAlertPayload(): ?string
    {
        return $this->alertPayload;
    }

    public function setAlertPayload(?string $alertPayload): void
    {
        $this->alertPayload = $alertPayload;
    }

    public function getLastCommand(): ?string
    {
        return $this->lastCommand;
    }

    public function setLastCommand(?string $lastCommand): void
    {
        $this->lastCommand = $lastCommand;
    }

    public function getStatusMessage(): StatusMessage
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(StatusMessage $statusMessage): void
    {
        $this->statusMessage = $statusMessage;
    }

    /**
     * @throws UnresolvableArgumentException
     */
    private function checkStatusType(?int $status): void
    {
        if (EntityStatusEnum::tryFrom($status) === null) {
            throw UnresolvableArgumentException::argumentIsNotSet('Fire security device status');
        }
    }
}