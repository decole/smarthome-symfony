<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\HumiditySensor;
use App\Domain\Sensor\Entity\PressureSensor;
use App\Domain\Sensor\Entity\TemperatureSensor;

class TemperatureSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(
        private readonly TemperatureSensor|HumiditySensor|PressureSensor $device,
        private readonly DevicePayload $payload
    ) {
    }

    final public function validate(): bool
    {
        return (int)$this->device->getPayloadMin() < (int)$this->payload->getPayload() ||
            (int)$this->device->getPayloadMax() > (int)$this->payload->getPayload();
    }

    final public function isAlert(): bool
    {
        return !$this->validate();
    }
}