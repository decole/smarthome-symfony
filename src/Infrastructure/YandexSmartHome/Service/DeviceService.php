<?php

namespace App\Infrastructure\YandexSmartHome\Service;

use Modules\AliceSmartHome\Services\Devices\DeviceInterface;

class DeviceService
{
    public function getDevice(string $id): DeviceInterface
    {
        foreach ($this->devices as $device) {
            if ($device->getDevice()->id == $id) {
                return $device;
            }
        }

        throw new Exception('device not found on device list');
    }
}