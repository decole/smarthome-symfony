<?php

namespace App\Domain\Security\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\CrudCommonFields;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Security\Enum\SecurityStateEnum;
use App\Domain\Security\Enum\SecurityTypeEnum;

final class Security implements EntityInterface
{
    use Entity, CreatedAt, UpdatedAt, CrudCommonFields;

    public const TYPE_TRANSCRIBES = [
        'mqtt_security_device' => 'mqtt датчик',
        'api_security_device' => 'api датчик',
    ];

    public function __construct(
        private string $securityType,
        private string $name,
        private string $topic,
        private ?string $payload,

        private ?string $detectPayload,
        private ?string $holdPayload,
        private ?string $lastCommand,

        private array $params,

        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify
    ) {
        $this->identify();
        $this->onCreated();

        $this->checkStatusType($status);
        $this->checkSecurityType($securityType);
    }

    public static function alias(): string
    {
        return 'security';
    }

    public function getType(): string
    {
        return $this->securityType;
    }

    public function setType(string $securityType): void
    {
        $this->securityType = $securityType;
    }

    public function getDetectPayload(): ?string
    {
        return $this->detectPayload;
    }

    public function setDetectPayload(?string $detectPayload): void
    {
        $this->detectPayload = $detectPayload;
    }

    public function getHoldPayload(): ?string
    {
        return $this->holdPayload;
    }

    public function setHoldPayload(?string $holdPayload): void
    {
        $this->holdPayload = $holdPayload;
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

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function isGuarded(): bool
    {
        return $this->lastCommand === SecurityStateEnum::GUARD_STATE->value;
    }

    private function checkStatusType(int $status): void
    {
        if (EntityStatusEnum::tryFrom($status) === null) {
            throw UnresolvableArgumentException::argumentIsNotSet('Security device status');
        }
    }

    private function checkSecurityType(string $type): void
    {
        if (SecurityTypeEnum::tryFrom($type) === null) {
            throw UnresolvableArgumentException::argumentIsNotSet('Security device type');
        }
    }
}