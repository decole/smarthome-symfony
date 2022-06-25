<?php

namespace App\Domain\Doctrine\Relay\Entity;

use App\Domain\Doctrine\Common\Traits\CreatedAt;
use App\Domain\Doctrine\Common\Traits\Entity;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;
use App\Domain\Doctrine\DeviceCommon\Entity\StatusMessage;
use App\Infrastructure\Doctrine\Interfaces\EntityInterface;
use Webmozart\Assert\Assert;

final class Relay implements EntityInterface
{
    public const STATUS_WARNING = 2;
    public const STATUS_ACTIVE = 1;
    public const STATUS_DEACTIVATE = 0;

    public const STATUS_MAP = [
        self::STATUS_ACTIVE,
        self::STATUS_DEACTIVATE,
        self::STATUS_WARNING,
    ];

    public const DRY_RELAY_TYPE = 'relay';
    public const WATERING_SWIFT_TYPE = 'swift';

    public const RELAY_TYPES = [
        self::DRY_RELAY_TYPE,
        self::WATERING_SWIFT_TYPE,
    ];

    public const TYPE_TRANSCRIBES = [
        self::DRY_RELAY_TYPE => 'реле',
        self::WATERING_SWIFT_TYPE => 'клапан автополива',
    ];

    use Entity, CreatedAt, UpdatedAt;

    public function __construct(
        private string $type,
        private string $name,
        private string $topic,
        private ?string $payload,
        private string $commandOn,
        private string $commandOff,
        private ?string $checkTopic,
        private ?string $checkTopicPayloadOn,
        private ?string $checkTopicPayloadOff,
        private ?string $lastCommand,
        private bool $isFeedbackPayload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,
    ) {
        $this->identify();
        $this->onCreated();
        $this->checkStatusType($status);
        $this->checkRelayType($type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(?string $payload): void
    {
        $this->payload = $payload;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->checkRelayType($type);
        $this->type = $type;
    }

    public function getCheckTopic(): ?string
    {
        return $this->checkTopic;
    }

    public function setCheckTopic(?string $checkTopic): void
    {
        $this->checkTopic = $checkTopic;
    }

    public function getCommandOn(): string
    {
        return $this->commandOn;
    }

    public function setCommandOn(string $commandOn): void
    {
        $this->commandOn = $commandOn;
    }

    public function getCommandOff(): string
    {
        return $this->commandOff;
    }

    public function setCommandOff(string $commandOff): void
    {
        $this->commandOff = $commandOff;
    }

    public function getCheckTopicPayloadOn(): ?string
    {
        return $this->checkTopicPayloadOn;
    }

    public function setCheckTopicPayloadOn(?string $checkTopicPayloadOn): void
    {
        $this->checkTopicPayloadOn = $checkTopicPayloadOn;
    }

    public function getCheckTopicPayloadOff(): ?string
    {
        return $this->checkTopicPayloadOff;
    }

    public function setCheckTopicPayloadOff(?string $checkTopicPayloadOff): void
    {
        $this->checkTopicPayloadOff = $checkTopicPayloadOff;
    }

    public function getLastCommand(): ?string
    {
        return $this->lastCommand;
    }

    public function setLastCommand(?string $lastCommand): void
    {
        $this->lastCommand = $lastCommand;
    }

    public function isFeedbackPayload(): bool
    {
        return $this->isFeedbackPayload;
    }

    public function setIsFeedbackPayload(bool $isFeedbackPayload): void
    {
        $this->isFeedbackPayload = $isFeedbackPayload;
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

    private function checkStatusType(int $status): void
    {
        Assert::inArray($status, self::STATUS_MAP, 'Sensor status not defined');
    }

    private function checkRelayType(string $type): void
    {
        Assert::inArray($type, self::RELAY_TYPES);
    }
}