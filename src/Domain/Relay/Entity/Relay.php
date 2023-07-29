<?php

declare(strict_types=1);

namespace App\Domain\Relay\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\CrudCommonFields;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Relay\Enum\RelayTypeEnum;

final class Relay implements EntityInterface
{
    /**
     * @see App\Domain\Relay\Enum\RelayTypeEnum
     */
    public const TYPE_TRANSCRIBES = [
        'relay' => 'реле',
        'swift' => 'клапан автополива',
    ];

    use Entity, CreatedAt, UpdatedAt, CrudCommonFields;

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
        private bool $notify
    ) {
        $this->identify();
        $this->onCreated();
        $this->checkStatusType($status);
        $this->checkRelayType($type);
    }

    public static function alias(): string
    {
        return 'relay';
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

    /**
     * @throws UnresolvableArgumentException
     */
    private function checkStatusType(int $status): void
    {
        if (EntityStatusEnum::tryFrom($status) === null) {
            throw UnresolvableArgumentException::argumentIsNotSet('Relay device status');
        }
    }

    /**
     * @throws UnresolvableArgumentException
     */
    private function checkRelayType(string $type): void
    {
        if (RelayTypeEnum::tryFrom($type) === null) {
            throw UnresolvableArgumentException::argumentIsNotSet('Relay device type');
        }
    }
}