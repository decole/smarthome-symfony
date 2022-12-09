<?php

namespace App\Domain\DeviceData\Entity;

use App\Domain\Contract\Repository\EntityInterface;

class DeviceDataValidated
{
    public function __construct(private bool $state, private EntityInterface $device)
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