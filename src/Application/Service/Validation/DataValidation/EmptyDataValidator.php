<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\Doctrine\EmptyDevice\Entity\EmptyDevice;

final class EmptyDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidatedDto
    {
        return new DeviceDataValidatedDto(true, new EmptyDevice());
    }
}