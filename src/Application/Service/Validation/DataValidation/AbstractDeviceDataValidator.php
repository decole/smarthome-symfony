<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Payload\Entity\DevicePayload;

abstract class AbstractDeviceDataValidator
{
    public function __construct(protected DevicePayload $payload, protected EntityInterface $device) {}

    abstract protected function validate(DeviceDataValidatedDto $dto): void;

    final public function createDto(): DeviceDataValidatedDto
    {
        return new DeviceDataValidatedDto(
            devicePayload: $this->payload,
            device: $this->device,
            hasCheckStatusWarning: false,
            hasAlertingNotify: false,
        );
    }

    final public function handle(): DeviceDataValidatedDto
    {
        $dto = $this->createDto();

        if (!$this->device->isNotify()) {
            return $dto;
        }

        $this->validate($dto);

        return $dto;
    }
}
