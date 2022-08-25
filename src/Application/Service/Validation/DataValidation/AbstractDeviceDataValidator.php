<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Payload\DevicePayload;

abstract class AbstractDeviceDataValidator
{
    public function __construct(protected DevicePayload $payload, protected EntityInterface $device)
    {
    }

    protected function createDto(bool $state, EntityInterface $device): DeviceDataValidatedDto
    {
        return new DeviceDataValidatedDto($state, $device);
    }
}
