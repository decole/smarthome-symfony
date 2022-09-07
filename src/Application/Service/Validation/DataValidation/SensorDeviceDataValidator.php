<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\Factory\SensorDeviceDataTypeValidatorFactory;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;

final class SensorDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidatedDto
    {
        if (!$this->device->isNotify()) {
            return $this->createDto(true, $this->device);
        }

        $state = ((new SensorDeviceDataTypeValidatorFactory())->create($this->device))->validate($this->payload);

        return $this->createDto($state, $this->device);
    }
}
