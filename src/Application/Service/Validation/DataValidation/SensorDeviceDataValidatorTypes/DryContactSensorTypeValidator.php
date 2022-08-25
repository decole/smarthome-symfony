<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Doctrine\Sensor\Entity\SensorDryContact;
use App\Domain\Payload\DevicePayload;

final class DryContactSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(private SensorDryContact $device)
    {
    }

    public function validate(DevicePayload $payload): bool
    {
        return (string)$this->device->getPayloadLow() === $payload->getPayload() ||
            (string)$this->device->getPayloadHigh() === $payload->getPayload();
    }
}
