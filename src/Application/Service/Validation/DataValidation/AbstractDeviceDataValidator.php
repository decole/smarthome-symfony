<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Payload\Entity\DevicePayload;

abstract class AbstractDeviceDataValidator
{
    public function __construct(protected DevicePayload $payload, protected EntityInterface $device)
    {
    }

    public function createDto(?bool $state, EntityInterface $device, bool $isAlert): DeviceDataValidatedDto
    {
        return new DeviceDataValidatedDto(
            state: $state,
            device: $device,
            isAlerting: $isAlert
        );
    }
}