<?php

namespace App\Application\Service\DeviceData\Dto;

use App\Domain\Contract\Repository\EntityInterface;

class DeviceDataValidatedDto
{
    public function __construct(private bool $state, private EntityInterface $device)
    {
    }

    public function isValid(): bool
    {
        return $this->state;
    }

    public function getDevice(): EntityInterface
    {
        return $this->device;
    }
}