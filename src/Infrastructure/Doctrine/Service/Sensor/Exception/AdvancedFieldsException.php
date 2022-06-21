<?php


namespace App\Infrastructure\Doctrine\Service\Sensor\Exception;


use App\Application\Exception\HandledException;

class AdvancedFieldsException extends HandledException
{
    public static function deviceTypeNotFound(string $type): self
    {
        return new self("Advanced fields for device type'{$type}' not found.");
    }
}