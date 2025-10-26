<?php

declare(strict_types=1);

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

    public static function processInterrupted(): self
    {
        return new self('Process interrupt by mqtt protocol');
    }
}
