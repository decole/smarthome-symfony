<?php

declare(strict_types=1);

namespace App\Domain\Sensor\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\CrudCommonFields;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;
use App\Infrastructure\Repository\Sensor\SensorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
#[ORM\Table(name: 'sensor')]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'sensor_type', type: 'string')]
#[DiscriminatorMap([
    'temperature' => TemperatureSensor::class,
    'humidity' => HumiditySensor::class,
    'leakage' => LeakageSensor::class,
    'pressure' => PressureSensor::class,
    'dryContact' => DryContactSensor::class,
])]
class Sensor implements EntityInterface
{
    use CreatedAt;
    use CrudCommonFields;
    use Entity;
    use UpdatedAt;

    public const TYPE = 'sensor';
    public const DISCRIMINATOR_MAP = [
        TemperatureSensor::TYPE => TemperatureSensor::class,
        HumiditySensor::TYPE => HumiditySensor::class,
        LeakageSensor::TYPE => LeakageSensor::class,
        PressureSensor::TYPE => PressureSensor::class,
        DryContactSensor::TYPE => DryContactSensor::class,
    ];
    public const SENSOR_TYPES = [
        TemperatureSensor::TYPE,
        HumiditySensor::TYPE,
        LeakageSensor::TYPE,
        PressureSensor::TYPE,
        DryContactSensor::TYPE,
    ];
    public const TYPE_TRANSCRIBES = [
        TemperatureSensor::TYPE => 'сенсор температуры',
        HumiditySensor::TYPE => 'сенсор влажности',
        LeakageSensor::TYPE => 'датчик протечки',
        PressureSensor::TYPE => 'сенсор давления',
        DryContactSensor::TYPE => 'датчик сухого контакта',
    ];

    public function __construct(
        #[ORM\Column(type: Types::STRING, unique: true)]
        #[Assert\NotBlank]
        private string $name,
        #[ORM\Column(type: Types::STRING, unique: true)]
        #[Assert\NotBlank]
        private string $topic,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payload,
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
        return 'sensor';
    }

    final public function getType(): string
    {
        return static::TYPE;
    }

    final public function getStatusMessage(): StatusMessage
    {
        return $this->statusMessage;
    }

    final public function setStatusMessage(StatusMessage $message): void
    {
        $this->statusMessage = $message;
    }

    final public function getStatus(): int
    {
        return $this->status;
    }

    final public function setStatus(int $status): void
    {
        $this->checkStatusType($status);
        $this->status = $status;
    }

    final public function isNotify(): bool
    {
        return $this->notify;
    }

    final public function setNotify(bool $isNotify): void
    {
        $this->notify = $isNotify;
    }

    /**
     * @throws UnresolvableArgumentException
     */
    private function checkStatusType(int $status): void
    {
        if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
            throw UnresolvableArgumentException::argumentIsNotSet('Status');
        }
    }
}
