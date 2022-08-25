<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;

final class FireSecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidatedDto
    {
        assert($this->device instanceof FireSecurity);

        $state = (string)$this->device->getAlertPayload() !== $this->payload->getPayload();

        return $this->createDto($state, $this->device);
    }
}
