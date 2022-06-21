<?php


namespace App\Application\Exception;


abstract class HandledException extends \Exception
{
    public static function getType(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

    final protected static function format(string $message, ...$args): HandledException
    {
        return new static(vsprintf($message, $args));
    }
}
