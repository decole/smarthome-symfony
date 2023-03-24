<?php

namespace App\Infrastructure\YandexSmartHome\Device;

use App\Infrastructure\YandexSmartHome\Dto\DeviceDto;

interface DeviceInterface
{
    public function getDevice(): DeviceDto;

    public function getId(): string;
}