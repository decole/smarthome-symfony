<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\FireSecurity\Entity\FireSecurity;

final class FireSecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    /**
     * @var FireSecurity $device
     */

    /**
     * @return DeviceDataValidatedDto
     */
    public function handle(): DeviceDataValidatedDto
    {
        $stateNormal = $this->device->getNormalPayload() === $this->payload->getPayload();

        return $this->createDto($stateNormal, $this->device, !$stateNormal);
    }
}