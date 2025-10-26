<?php

declare(strict_types=1);

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use App\Infrastructure\Security\Api\ApiSecureService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RelayDeviceOperateApiController extends AbstractFOSRestController
{
    public function __construct(
        private readonly MqttHandleService $service,
        private readonly ApiSecureService $apiSecureService,
    ) {}

    #[Route('/device/send')]
    public function send(Request $request): Response
    {
        $topic = $request->request->get('topic');
        $payload = $request->request->get('payload');
        $secureToken = $request->request->get('token');

        if (null === $topic || null === $payload) {
            return new JsonResponse([
                'error' => 'empty post data',
            ], Response::HTTP_BAD_REQUEST);
        }

        $message = new DevicePayload(
            topic: $topic,
            payload: $payload,
        );

        if ($this->apiSecureService->validate($secureToken)) {
            $this->service->post($message);
        }

        return new JsonResponse([
            'status' => 'success',
        ]);
    }
}
