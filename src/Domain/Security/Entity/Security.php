<?php

declare(strict_types=1);

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
use App\Infrastructure\Repository\Security\SecurityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: SecurityRepository::class)]
#[ORM\Table(name: 'security')]
class Security implements EntityInterface
{
    use CreatedAt;
    use CrudCommonFields;
    use Entity;
    use UpdatedAt;

    public const TYPE_TRANSCRIBES = [
        'mqtt_security_device' => 'mqtt датчик',
        'api_security_device' => 'api датчик',
    ];

    public function __construct(
        #[ORM\Column(type: Types::STRING)]
        private string $securityType,
        #[ORM\Column(type: Types::STRING, unique: true)]
        private string $name,
        #[ORM\Column(type: Types::STRING, unique: true)]
        private string $topic,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payload,
        #[ORM\Column(type: Types::STRING)]
        private ?string $detectPayload,
        #[ORM\Column(type: Types::STRING)]
        private ?string $holdPayload,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $lastCommand,
        #[ORM\Column(type: Types::JSON)]
        private array $params,
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
        if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
            throw UnresolvableArgumentException::argumentIsNotSet('Security device status');
        }
    }

    private function checkSecurityType(string $type): void
    {
        if (!SecurityTypeEnum::tryFrom($type) instanceof SecurityTypeEnum) {
            throw UnresolvableArgumentException::argumentIsNotSet('Security device type');
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
