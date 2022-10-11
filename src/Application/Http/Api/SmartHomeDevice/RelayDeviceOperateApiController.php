<?php

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Domain\Payload\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RelayDeviceOperateApiController
{
    public function __construct(private MqttHandleService $service)
    {
    }

    #[Route('/device/send')]
    public function send(Request $request): Response
    {
        $message = new DevicePayload(
            topic: $request->request->get('topic'),
            payload: $request->request->get('payload')
        );

        $this->service->post($message);

        return new JsonResponse([
            'status' => 'success',
        ]);
    }
}