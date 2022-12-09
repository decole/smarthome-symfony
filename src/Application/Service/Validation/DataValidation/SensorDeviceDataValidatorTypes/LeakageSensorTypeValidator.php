<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\LeakageSensor;

final class LeakageSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(private LeakageSensor $device)
    {
    }

    public function validate(DevicePayload $payload): bool
    {
        return (string)$this->device->getPayloadDry() === $payload->getPayload();
    }
}