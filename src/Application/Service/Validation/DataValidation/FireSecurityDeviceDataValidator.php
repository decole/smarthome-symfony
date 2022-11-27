<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidated;
use App\Domain\FireSecurity\Entity\FireSecurity;

final class FireSecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidated
    {
        assert($this->device instanceof FireSecurity);

        $state = (string)$this->device->getNormalPayload() === $this->payload->getPayload();

        return $this->createDto($state, $this->device);
    }
}