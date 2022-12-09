<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidated;
use App\Domain\Payload\Entity\DevicePayload;

abstract class AbstractDeviceDataValidator
{
    public function __construct(protected DevicePayload $payload, protected EntityInterface $device)
    {
    }

    protected function createDto(bool $state, EntityInterface $device): DeviceDataValidated
    {
        return new DeviceDataValidated($state, $device);
    }
}