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

        $secureState = $this->payload->getPayload() === (string)$this->device->getDetectPayload();

        return $this->createDto($secureState, $this->device);
    }
}