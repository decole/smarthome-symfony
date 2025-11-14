<?php

declare(strict_types=1);

namespace App\Domain\FireSecurity\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\CrudCommonFields;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;
use App\Infrastructure\Repository\FireSecurity\FireSecurityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FireSecurityRepository::class)]
#[ORM\Table(name: 'fire_security')]
final class FireSecurity implements EntityInterface
{
    use CreatedAt;
    use CrudCommonFields;
    use Entity;
    use UpdatedAt;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $topic;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $payload;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $normalPayload;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $alertPayload;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $lastCommand;

    #[Embedded(class: StatusMessage::class)]
    private StatusMessage $statusMessage;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $notify;

    public function __construct(
        #[ORM\Column(type: Types::SMALLINT)]
        private int $status,
    ) {
        $this->identify();
        $this->onCreated();
        $this->statusMessage = new StatusMessage();

        $this->checkStatusType($this->status);
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
        if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
            throw UnresolvableArgumentException::argumentIsNotSet('Fire security device status');
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
