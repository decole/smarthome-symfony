<?php

namespace App\Domain\Contract\Service\Validation\DataValidation;

use App\Domain\DeviceData\Entity\DeviceDataValidated;

interface DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidated;
}