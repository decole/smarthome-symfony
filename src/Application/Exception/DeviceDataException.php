<?php

namespace App\Application\Exception;

class DeviceDataException extends HandledException
{
    public static function notFoundValidatorType(): self
    {
        return new self('Not found DeviceDataValidator by current device type');
    }
}
