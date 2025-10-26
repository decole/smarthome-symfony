<?php

declare(strict_types=1);

namespace App\Domain\PLC\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;
use App\Infrastructure\Repository\PLC\PlcRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlcRepository::class)]
#[ORM\Table(name: 'plc')]
final class PLC implements EntityInterface
{
    use CreatedAt;
    use Entity;
    use UpdatedAt;

    public function __construct(
        #[ORM\Column(type: Types::STRING, unique: true)]
        #[Assert\NotBlank]
        private string $name,
        #[ORM\Column(type: Types::STRING, unique: true)]
        #[Assert\NotBlank]
        private string $targetTopic,
        #[ORM\Column(type: Types::INTEGER)]
        private int $alarmSecondDelay,
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
        if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
            throw UnresolvableArgumentException::argumentIsNotSet('PLC status');
        }
    }
}
