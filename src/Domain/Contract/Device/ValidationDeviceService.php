<?php

namespace App\Domain\Contract\Device;

use App\Domain\Payload\Dto\MessageDto;

interface ValidationDeviceService
{
    public function validate(MessageDto $message): bool;
}