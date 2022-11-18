<?php

namespace App\Domain\Doctrine\Sensor\Entity;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\Common\Embedded\StatusMessage;
use App\Domain\Doctrine\Common\Traits\CreatedAt;
use App\Domain\Doctrine\Common\Traits\CrudCommonFields;
use App\Domain\Doctrine\Common\Traits\Entity;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;
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

    use Entity, CreatedAt, UpdatedAt, CrudCommonFields;

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify
    ) {
        $this->identify();
        $this->onCreated();
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

    private function checkStatusType(int $status): void
    {
        Assert::inArray($status, self::STATUS_MAP, 'Sensor status not defined');
    }
}