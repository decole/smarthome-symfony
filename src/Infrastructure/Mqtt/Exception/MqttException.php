<?php

declare(strict_types=1);

namespace App\Infrastructure\Mqtt\Exception;

use Exception;

class MqttException extends Exception
{
    public static function disconnect(): self
    {
        return new self('Loose connect by mqtt broker');
    }
}