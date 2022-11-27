<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\HumiditySensor;
use App\Domain\Sensor\Entity\PressureSensor;
use App\Domain\Sensor\Entity\TemperatureSensor;

class TemperatureSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(private TemperatureSensor|HumiditySensor|PressureSensor $device)
    {
    }

    final public function validate(DevicePayload $payload): bool
    {
        return !((int)$this->device->getPayloadMin() > (int)$payload->getPayload() ||
            (int)$this->device->getPayloadMax() < (int)$payload->getPayload());
    }
}