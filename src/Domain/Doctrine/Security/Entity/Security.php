<?php

namespace App\Domain\Doctrine\Security\Entity;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\Common\Embedded\StatusMessage;
use App\Domain\Doctrine\Common\Traits\CreatedAt;
use App\Domain\Doctrine\Common\Traits\CrudCommonFields;
use App\Domain\Doctrine\Common\Traits\Entity;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;
use Webmozart\Assert\Assert;

final class Security implements EntityInterface
{
    use Entity, CreatedAt, UpdatedAt, CrudCommonFields;

    public const STATUS_WARNING = 2;
    public const STATUS_ACTIVE = 1;
    public const STATUS_DEACTIVATE = 0;

    public const STATUS_MAP = [
        self::STATUS_ACTIVE,
        self::STATUS_DEACTIVATE,
        self::STATUS_WARNING,
    ];

    public const MQTT_TYPE = 'mqtt_security_device';
    public const API_TYPE = 'api_security_device';

    public const SECURITY_TYPES = [
        self::MQTT_TYPE,
        self::API_TYPE,
    ];

    public const TYPE_TRANSCRIBES = [
        self::MQTT_TYPE => 'mqtt датчик',
        self::API_TYPE => 'api датчик',
    ];

    public const GUARD_STATE = 'guard';
    public const HOLD_STATE = 'hold';

    public const GUARD_STATE_MAP = [
        self::GUARD_STATE,
        self::HOLD_STATE,
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

    public function isGuard(): bool
    {
        return $this->getLastCommand() === self::GUARD_STATE;
    }

    public function setGuardState(string $state): void
    {
        Assert::inArray($state, self::GUARD_STATE_MAP);

        $this->lastCommand = $state;
    }

    private function checkStatusType(int $status): void
    {
        Assert::inArray($status, self::STATUS_MAP, 'Security device status not defined');
    }

    private function checkSecurityType(string $type): void
    {
        Assert::inArray($type, self::SECURITY_TYPES);
    }
}
