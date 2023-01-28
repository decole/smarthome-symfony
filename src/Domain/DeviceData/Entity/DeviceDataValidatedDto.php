<?php

namespace App\Domain\DeviceData\Entity;

use App\Domain\Contract\Repository\EntityInterface;

final class DeviceDataValidatedDto
{
    public function __construct(
        public readonly ?bool $state,
        public readonly EntityInterface $device,
        public readonly bool $isAlerting
    ) {
    }
}