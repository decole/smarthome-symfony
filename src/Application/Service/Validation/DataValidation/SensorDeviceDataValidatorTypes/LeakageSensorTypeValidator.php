<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Doctrine\Sensor\Entity\SensorLeakage;
use App\Domain\Payload\DevicePayload;

final class LeakageSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(private SensorLeakage $device)
    {
    }

    public function validate(DevicePayload $payload): bool
    {
        return (string)$this->device->getPayloadDry() === $payload->getPayload();
    }
}
