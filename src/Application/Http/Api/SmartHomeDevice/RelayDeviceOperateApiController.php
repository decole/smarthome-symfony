<?php

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Infrastructure\Security\Api\ApiSecureService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RelayDeviceOperateApiController
{
    public function __construct(
        private readonly MqttHandleService $service,
        private readonly ApiSecureService $apiSecureService
    ) {
    }

    #[Route('/device/send')]
    public function send(Request $request): Response
    {
        $topic = $request->request->get('topic');
        $payload = $request->request->get('payload');
        $secureToken = $request->request->get('token');

        if ($topic === null || $payload === null) {
            return new JsonResponse([
                'error' => 'empty post data'
            ], 400);
        }

        $message = new DevicePayload(
            topic: $topic,
            payload: $payload
        );

        if ($this->apiSecureService->validate($secureToken)) {
            $this->service->post($message);
        }

        return new JsonResponse([
            'status' => 'success',
        ]);
    }
}