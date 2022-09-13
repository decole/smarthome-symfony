<?php

namespace App\Infrastructure\YandexSmartHome\Device;

use Modules\AliceSmartHome\Services\Dto\DeviceDto;

interface DeviceInterface
{
    public function getDevice(): DeviceDto;

    public function getId(): string;
}