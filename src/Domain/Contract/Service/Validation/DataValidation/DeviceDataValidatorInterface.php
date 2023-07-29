<?php

declare(strict_types=1);

namespace App\Domain\Contract\Service\Validation\DataValidation;

use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;

interface DeviceDataValidatorInterface
{
    public function handle(): DeviceDataValidatedDto;
}