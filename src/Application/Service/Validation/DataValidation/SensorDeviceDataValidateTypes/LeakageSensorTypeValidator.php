<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\LeakageSensor;

final class LeakageSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(
        private readonly LeakageSensor $device,
        private readonly DevicePayload $payload
    )
    {
    }

    public function validate(): bool
    {
        return (string)$this->device->getPayloadDry() === $this->payload->getPayload() ||
            (string)$this->device->getPayloadWet() === $this->payload->getPayload();
    }

    public function isAlert(): bool
    {
        return (string)$this->device->getPayloadWet() === $this->payload->getPayload();
    }
}