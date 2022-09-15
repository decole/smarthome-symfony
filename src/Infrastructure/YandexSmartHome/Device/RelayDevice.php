<?php

namespace App\Infrastructure\YandexSmartHome\Device;

use App\Infrastructure\YandexSmartHome\Dto\DeviceDto;
use App\Infrastructure\YandexSmartHome\Schema\RelaySchema;

class RelayDevice extends AbstractDevice implements DeviceInterface
{
    private ?string $state = null;

    public function getDevice(): DeviceDto
    {
        $device = new DeviceDto();
        $device->id = $this->getId();
        $device->state = $this->state;
        $device->type = self::RELAY;
        $device->schema = new RelaySchema($this->getId());

        return $device;
    }

    public function getId(): string
    {
        return 'default';
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}