<?php

declare(strict_types=1);

namespace App\Infrastructure\YandexSmartHome\Device;

class LampMargulisDevice extends RelayDevice
{
    public function getId(): string
    {
        return '1';
    }
}