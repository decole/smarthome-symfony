<?php

namespace App\Domain\Contract\Device;

use App\Domain\Payload\Entity\DevicePayload;

interface ValidationDeviceService
{
    public function validate(DevicePayload $message): bool;
}