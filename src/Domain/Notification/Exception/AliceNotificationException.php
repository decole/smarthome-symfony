<?php

namespace App\Domain\Notification\Exception;

use Exception;

class AliceNotificationException extends Exception
{
    public static function manyCharacters(): self
    {
        return new self('Too many characters (100 characters limit)');
    }

    public static function notUsingMethod(): self
    {
        return new self('Not use getTo() method by AliceNotification');
    }
}