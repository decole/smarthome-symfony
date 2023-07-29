<?php

declare(strict_types=1);

namespace App\Infrastructure\YandexSmartHome\Device;

use App\Infrastructure\YandexSmartHome\Dto\DeviceDto;

interface DeviceInterface
{
    public function getDevice(): DeviceDto;

    public function getId(): string;
}