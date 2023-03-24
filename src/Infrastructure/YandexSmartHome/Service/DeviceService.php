<?php

namespace App\Infrastructure\YandexSmartHome\Service;

use App\Infrastructure\YandexSmartHome\Device\DeviceInterface;
use App\Infrastructure\YandexSmartHome\Device\LampMargulisDevice;

final class DeviceService
{
    public function getDevice(string $id): ?DeviceInterface
    {
        foreach ($this->getDeviceList() as $deviceClass) {
            $device = new $deviceClass();

            if ($device->getDevice()->id == $id) {
                return $device;
            }
        }

        return null;
    }

    private function getDeviceList(): array
    {
        return [
            LampMargulisDevice::class,
        ];
    }
}