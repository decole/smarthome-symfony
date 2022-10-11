<?php

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceTopicPayloadPresenter;
use App\Application\Service\DeviceData\SecureDeviceDataService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecureDeviceTopicPayloadApiController
{
    public function __construct(private SecureDeviceDataService $service)
    {
    }

    #[Route('/secure/state')]
    public function secureTopicState(Request $request): Response
    {
        $topic = $request->get('topic');

        if ($topic === null) {
            return new JsonResponse([
                'error' => 'empty topics'
            ], 400);
        }

        return new JsonResponse(
            (new SecureDeviceTopicPayloadPresenter($this->service->getDeviceState($topic)))->present()
        );
    }
}