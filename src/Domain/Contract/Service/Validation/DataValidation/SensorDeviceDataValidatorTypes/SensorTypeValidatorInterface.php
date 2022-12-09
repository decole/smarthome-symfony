<?php

namespace App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Payload\Entity\DevicePayload;

interface SensorTypeValidatorInterface
{
    public function validate(DevicePayload $payload): bool;
}