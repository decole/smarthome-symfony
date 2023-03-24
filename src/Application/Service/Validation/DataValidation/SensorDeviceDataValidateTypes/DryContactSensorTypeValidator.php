<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\DryContactSensor;

final class DryContactSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(
        private readonly DryContactSensor $device,
        private readonly DevicePayload $payload
    ) {
    }

    public function validate(): bool
    {
        return (string)$this->device->getPayloadLow() === $this->payload->getPayload() ||
            (string)$this->device->getPayloadHigh() === $this->payload->getPayload();
    }

    public function isAlert(): bool
    {
        return !$this->validate();
    }
}