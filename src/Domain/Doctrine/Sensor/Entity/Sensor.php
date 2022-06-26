<?php


namespace App\Domain\Doctrine\Sensor\Entity;


use App\Domain\Doctrine\Common\Traits\CreatedAt;
use App\Domain\Doctrine\Common\Traits\CrudCommonFields;
use App\Domain\Doctrine\Common\Traits\Entity;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;
use App\Domain\Doctrine\DeviceCommon\Entity\StatusMessage;
use App\Infrastructure\Doctrine\Interfaces\EntityInterface;
use Webmozart\Assert\Assert;

class Sensor implements EntityInterface
{
    public const TYPE = 'sensor';

    public const STATUS_WARNING = 2;
    public const STATUS_ACTIVE = 1;
    public const STATUS_DEACTIVATE = 0;

    public const STATUS_MAP = [
        self::STATUS_ACTIVE,
        self::STATUS_DEACTIVATE,
        self::STATUS_WARNING,
    ];

    public const DISCRIMINATOR_MAP = [
        SensorTemperature::TYPE => SensorTemperature::class,
        SensorHumidity::TYPE => SensorHumidity::class,
        SensorLeakage::TYPE => SensorLeakage::class,
        SensorPressure::TYPE => SensorPressure::class,
        SensorDryContact::TYPE => SensorDryContact::class,
    ];

    public const SENSOR_TYPES = [
        SensorTemperature::TYPE,
        SensorHumidity::TYPE,
        SensorLeakage::TYPE,
        SensorPressure::TYPE,
        SensorDryContact::TYPE,
    ];

    public const TYPE_TRANSCRIBES = [
        SensorTemperature::TYPE => 'сенсор температуры',
        SensorHumidity::TYPE => 'сенсор влажности',
        SensorLeakage::TYPE => 'датчик протечки',
        SensorPressure::TYPE => 'сенсор давления',
        SensorDryContact::TYPE => 'датчик сухого контакта',
    ];

    use Entity, CreatedAt, UpdatedAt, CrudCommonFields;

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,
    ) {
        $this->identify();
        $this->onCreated();
        $this->checkStatusType($status);
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

    final public function setUpdatedAt(): void
    {
        $this->onUpdated();
    }

    private function checkStatusType(int $status): void
    {
        Assert::inArray($status, self::STATUS_MAP, 'Sensor status not defined');
    }
}