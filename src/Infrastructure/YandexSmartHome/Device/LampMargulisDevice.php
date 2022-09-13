<?php

namespace App\Infrastructure\YandexSmartHome\Device;

class LampMargulisDevice extends RelayDevice implements DeviceInterface
{
    public function getId(): string
    {
        return '1';
    }
}