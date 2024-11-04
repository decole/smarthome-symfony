<?php

declare(strict_types=1);

namespace App\Infrastructure\YandexSmartHome\Service;

use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Infrastructure\YandexSmartHome\Device\DeviceInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

final class SmartHomeService
{
    public function __construct(private DeviceService $deviceService, private MqttHandleService $service)
    {
    }

    public function getRequestId(Request $request): string
    {
        return $request->headers->get('x-request-id') ?? 'bad-request-id';
    }

    /**
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

            $entity = $this->deviceService->getDevice($id);

            if ($entity instanceof \App\Infrastructure\YandexSmartHome\Device\DeviceInterface) {
                $result[] = $entity;
            }
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function relayAction(string $topic, $query): bool
    {
        if (!array_key_exists(0, $query->payload->devices) ||
            !array_key_exists(0, $query->payload->devices[0]->capabilities)
        ) {
            throw new Exception('not valid relay action state');
        }

        $state = $query->payload->devices[0]->capabilities[0]->state->value;

        $payload = $state ? 'on' : 'off';

        $this->service->post(new DevicePayload($topic, $payload));

        return $state;
    }
}