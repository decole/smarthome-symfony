<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\Factory\SensorDataValidateFactory;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Sensor\Entity\Sensor;

final class SensorDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function handle(): DeviceDataValidatedDto
    {
        if (!$this->device->isNotify()) {
            return $this->createDto(
                state: true,
                device: $this->device,
                isAlert: false
            );
        }

        $validator = (new SensorDataValidateFactory())->create($this->device, $this->payload);

        return $this->createDto(
            state: $validator->validate(),
            device: $this->device,
            isAlert: $validator->isAlert()
        );
    }
}