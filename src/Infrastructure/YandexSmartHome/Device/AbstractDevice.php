<?php

declare(strict_types=1);

namespace App\Infrastructure\YandexSmartHome\Device;

abstract class AbstractDevice
{
    public const TYPES = [
        self::SENSOR,
        self::RELAY,
    ];

    public const SENSOR = 'sensor';

    public const RELAY = 'relay';
}