<?php

namespace App\Infrastructure\YandexSmartHome\Device;

use Modules\AliceSmartHome\Services\Devices\Schemas\RelaySchema;
use Modules\AliceSmartHome\Services\Dto\DeviceDto;

class RelayDevice extends AbstractDevice implements DeviceInterface
{
    public function __construct(private mixed $state)
    {
    }

    public function getDevice(): DeviceDto
    {
        $device = new DeviceDto();
        $device->id = $this->getId();
        $device->state = $this->state;
        $device->type = self::RELAY;
        $device->schema = new RelaySchema($this->getId(), $this->getState());

        return $device;
    }

    public function getId(): string
    {
        return 'default';
    }

    public function getState(): mixed
    {
        return $this->state;
    }
}