<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Doctrine\Sensor\Entity\SensorHumidity;
use App\Domain\Doctrine\Sensor\Entity\SensorPressure;
use App\Domain\Doctrine\Sensor\Entity\SensorTemperature;
use App\Domain\Payload\DevicePayload;

class TemperatureSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(private SensorTemperature|SensorHumidity|SensorPressure $device)
    {
    }

    final public function validate(DevicePayload $payload): bool
    {
        return !((int)$this->device->getPayloadMin() > (int)$payload->getPayload() ||
            (int)$this->device->getPayloadMax() < (int)$payload->getPayload());
    }
}
