<?php

declare(strict_types=1);

namespace App\Application\Exception;

class SaveDeviceStateException extends \Exception
{
    public static function undefined(string $type): self
    {
        return new self("Periodic handling work for saving device payload failed found type {$type} of device");
    }
}
