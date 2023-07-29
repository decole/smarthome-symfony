<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\EmptyDevice\Entity\EmptyDevice;

final class EmptyDataValidator implements DeviceDataValidatorInterface
{
    public function handle(): DeviceDataValidatedDto
    {
        return new DeviceDataValidatedDto(
            state: null,
            device: new EmptyDevice(),
            isAlerting: false
        );
    }
}