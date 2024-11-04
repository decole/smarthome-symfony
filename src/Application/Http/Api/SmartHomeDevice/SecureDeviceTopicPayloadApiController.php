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

        if (mb_strlen($topic) == 0) {
            return new JsonResponse([
                'error' => 'empty topics'
            ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            (new SecureDeviceTopicPayloadPresenter($this->service->getDeviceState($topic)))->present()
        );
    }
}