<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes;

use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\DryContactSensor;

final readonly class DryContactSensorTypeValidator implements SensorTypeValidatorInterface
{
    public function __construct(
        private DryContactSensor $device,
        private DevicePayload $payload,
    ) {}

    public function validate(): bool
    {
        return (string) $this->device->getPayloadLow() === $this->payload->getPayload()
            || (string) $this->device->getPayloadHigh() === $this->payload->getPayload();
    }

    public function validateStatus(): bool
    {
        return $this->device->getPayload() !== $this->payload->getPayload();
    }
}
