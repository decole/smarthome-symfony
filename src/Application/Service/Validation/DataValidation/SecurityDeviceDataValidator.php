<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\Doctrine\Security\Entity\Security;

final class SecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidatedDto
    {
        assert($this->device instanceof Security);

        $isNormal = $this->payload->getPayload() === (string)$this->device->getHoldPayload();

        // true - нормальное состояние
        // false - обнаружено движение
        return $this->createDto($isNormal, $this->device);
    }
}