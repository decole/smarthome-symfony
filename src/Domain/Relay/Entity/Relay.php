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
use App\Infrastructure\Repository\Relay\RelayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RelayRepository::class)]
#[ORM\Table(name: 'relay')]
class Relay implements EntityInterface
{
    use CreatedAt;
    use CrudCommonFields;
    use Entity;
    use UpdatedAt;

    /**
     * @see RelayTypeEnum
     */
    public const TYPE_TRANSCRIBES = [
        'relay' => 'реле',
        'swift' => 'клапан автополива',
    ];

    public function __construct(
        #[ORM\Column(type: Types::STRING)]
        private string $type,
        #[ORM\Column(type: Types::STRING, unique: true)]
        #[Assert\NotBlank]
        private string $name,
        #[ORM\Column(type: Types::STRING, unique: true)]
        #[Assert\NotBlank]
        private string $topic,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payload,
        #[ORM\Column(type: Types::STRING)]
        private string $commandOn,
        #[ORM\Column(type: Types::STRING)]
        private string $commandOff,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $checkTopic,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $checkTopicPayloadOn,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $checkTopicPayloadOff,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $lastCommand,
        #[ORM\Column(type: Types::BOOLEAN)]
        private bool $isFeedbackPayload,
        #[Embedded(class: StatusMessage::class)]
        private StatusMessage $statusMessage,
        #[ORM\Column(type: Types::SMALLINT)]
        private int $status,
        #[ORM\Column(type: Types::BOOLEAN)]
        private bool $notify,
    ) {
        $this->identify();
        $this->onCreated();

        $this->statusMessage = new StatusMessage();

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
        if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
            throw UnresolvableArgumentException::argumentIsNotSet('Relay device status');
        }
    }

    /**
     * @throws UnresolvableArgumentException
     */
    private function checkRelayType(string $type): void
    {
        if (!RelayTypeEnum::tryFrom($type) instanceof RelayTypeEnum) {
            throw UnresolvableArgumentException::argumentIsNotSet('Relay device type');
        }
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
}
