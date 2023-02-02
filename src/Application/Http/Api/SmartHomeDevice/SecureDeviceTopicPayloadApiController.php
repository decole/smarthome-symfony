<?php

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceTopicPayloadPresenter;
use App\Domain\DeviceData\Service\SecureDeviceDataService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecureDeviceTopicPayloadApiController
{
    public function __construct(private readonly SecureDeviceDataService $service)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
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