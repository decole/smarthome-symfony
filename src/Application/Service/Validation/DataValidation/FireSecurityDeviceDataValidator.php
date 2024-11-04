<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;

final class FireSecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function handle(): DeviceDataValidatedDto
    {
        $stateNormal = $this->device->getNormalPayload() === $this->payload->getPayload();

        return $this->createDto($stateNormal, $this->device, !$stateNormal);
    }
}