<?php

declare(strict_types=1);

namespace App\Domain\Contract\Device;

use App\Domain\Payload\Entity\DevicePayload;

interface ValidationDeviceService
{
    public function validate(DevicePayload $message): bool;
}