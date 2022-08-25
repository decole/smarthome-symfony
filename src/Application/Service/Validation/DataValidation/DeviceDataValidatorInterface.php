<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;

interface DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidatedDto;
}
