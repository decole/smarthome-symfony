<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidated;
use App\Domain\Security\Entity\Security;

final class SecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidated
    {
        assert($this->device instanceof Security);

        $isNormal = $this->payload->getPayload() === (string)$this->device->getHoldPayload();

        // true - нормальное состояние
        // false - обнаружено движение
        return $this->createDto($isNormal, $this->device);
    }
}