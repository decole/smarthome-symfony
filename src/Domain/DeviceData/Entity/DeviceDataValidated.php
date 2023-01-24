<?php

namespace App\Domain\DeviceData\Entity;

use App\Domain\Contract\Repository\EntityInterface;

final class DeviceDataValidated
{
    public function __construct(private readonly bool $state, private readonly EntityInterface $device)
    {
    }

    public function isNormal(): bool
    {
        return $this->state;
    }

    public function getDevice(): EntityInterface
    {
        return $this->device;
    }
}