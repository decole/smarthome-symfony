<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\LeakageSensor;

final readonly class LeakageSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(
        private LeakageSensor $device,
        private DevicePayload $payload,
    ) {}

    public function validate(): bool
    {
        return $this->device->getPayloadWet() !== $this->payload->getPayload();
    }

    public function validateStatus(): bool
    {
        return $this->device->getPayloadDry() !== $this->payload->getPayload()
            && $this->device->getPayloadWet() !== $this->payload->getPayload();
    }
}
