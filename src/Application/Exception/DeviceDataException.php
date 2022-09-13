<?php

namespace App\Application\Exception;

class DeviceDataException extends HandledException
{
    public static function notFoundValidatorType(): self
    {
        return new self('Not found DeviceDataValidator by current device type');
    }

    public static function notFoundPageEntity(string $id): self
    {
        return new self("Not found page entity by id {$id}");
    }
}