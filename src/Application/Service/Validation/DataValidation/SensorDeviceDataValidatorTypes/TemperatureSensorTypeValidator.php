<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Doctrine\Sensor\Entity\HumiditySensor;
use App\Domain\Doctrine\Sensor\Entity\PressureSensor;
use App\Domain\Doctrine\Sensor\Entity\TemperatureSensor;
use App\Domain\Payload\DevicePayload;

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