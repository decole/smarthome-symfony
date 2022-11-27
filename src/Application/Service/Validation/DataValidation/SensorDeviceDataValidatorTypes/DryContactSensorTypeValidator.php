<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\DryContactSensor;

final class DryContactSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(private DryContactSensor $device)
    {
    }

    public function validate(DevicePayload $payload): bool
    {
        return (string)$this->device->getPayloadLow() === $payload->getPayload() ||
            (string)$this->device->getPayloadHigh() === $payload->getPayload();
    }
}