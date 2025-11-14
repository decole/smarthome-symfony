<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\Factory\SensorDataValidateTypeFactory;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Sensor\Entity\Sensor;

final class SensorDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(DeviceDataValidatedDto $dto): void
    {
        \assert($this->device instanceof Sensor);

        $validateType = (new SensorDataValidateTypeFactory())->create($this->device, $this->payload);

        $dto->hasAlertingNotify = !$validateType->validate();

        if ($this->device->getStatus()) {
            $dto->hasCheckStatusWarning = $validateType->validateStatus();
        }
    }
}
