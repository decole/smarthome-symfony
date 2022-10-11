<?php

namespace App\Application\Presenter\Api\YandexSmartHome;

use App\Application\Presenter\Api\PresenterInterface;
use App\Infrastructure\YandexSmartHome\Device\DeviceInterface;

final class DeviceListQueryPresenter implements PresenterInterface
{
    /**
     * @var DeviceInterface[]
     */
    private array $devices;

    private string $requestId;

    public function __construct(array $devices, string $requestId)
    {
        $this->devices = $devices;
        $this->requestId = $requestId;
    }

    public function present(): array
    {
        $devices = $this->getDeviceList();

        return [
            "request_id" => $this->requestId,
            "payload" => [
                "devices" => $devices
            ]
        ];
    }

    private function getDeviceList(): array
    {
        $devices = [];

        foreach ($this->devices as $device) {
            $devices[] = $device->getDevice()->schema->getSchema();
        }

        return $devices;
    }
}