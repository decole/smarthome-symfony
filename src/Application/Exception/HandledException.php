<?php

declare(strict_types=1);

namespace App\Application\Exception;

abstract class HandledException extends \Exception
{
    public static function getType(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

    final protected static function format(string $message, ...$args): self
    {
        return new static(vsprintf($message, $args));
    }
}
