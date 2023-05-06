<?php

namespace App\Infrastructure\Telegram\Exception;

use Exception;

class TelegramServiceException extends Exception
{
    public static function apiTokenEmpty(): self
    {
        return new self('Please configure telegram bot Token');
    }
}