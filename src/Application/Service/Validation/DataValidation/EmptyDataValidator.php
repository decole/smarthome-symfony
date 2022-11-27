<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidated;
use App\Domain\EmptyDevice\Entity\EmptyDevice;

final class EmptyDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidated
    {
        return new DeviceDataValidated(true, new EmptyDevice());
    }
}