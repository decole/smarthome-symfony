<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

use App\Domain\Payload\DevicePayload;

interface SensorTypeValidatorInterface
{
    public function validate(DevicePayload $payload): bool;
}
