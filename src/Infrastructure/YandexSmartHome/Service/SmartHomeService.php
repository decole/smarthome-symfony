<?php

namespace App\Infrastructure\YandexSmartHome\Service;

use Illuminate\Http\Request;
use Modules\AliceSmartHome\Services\Devices\DeviceInterface;
use Modules\AutoWatering\Events\MqttMessagePosting;

class SmartHomeService
{
    public function getRequestId(Request $request): string
    {
        if (isset($request->header()['x-request-id'])) {
            return current($request->header()['x-request-id']);
        }

        return 'bad-request-id';
    }

    /**
     * @param string|null $content
     * @return DeviceInterface[]
     * @throws Exception
     */
    public function devicesQuery(?string $content): array
    {
        $result = [];

        $json = json_decode($content);
        $devices = $json->devices ?? null;

        if (!$devices) {
            throw new Exception('field devises not found');
        }

        foreach ($devices as $device) {
            $id = $device->id ?? null;

            if (!$id) {
                continue;
            }

            $result[] = $this->deviceService->getDevice($id);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function relayAction(string $topic, $query): bool
    {
        if (!key_exists(0, $query->payload->devices) ||
            !key_exists(0, $query->payload->devices[0]->capabilities)
        ) {
            throw new Exception('not valid relay action state');
        }

        $state = $query->payload->devices[0]->capabilities[0]->state->value;

        $payload = $state ? 'on' : 'off';

        MqttMessagePosting::dispatch($topic, $payload);

        return $state;
    }
}