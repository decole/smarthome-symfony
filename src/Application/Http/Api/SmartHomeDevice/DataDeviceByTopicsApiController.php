<?php

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Application\Service\SitePage\ApiUriTranscribeService;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DataDeviceByTopicsApiController
{
    public function __construct(
        private readonly ApiUriTranscribeService $service,
        private readonly DeviceDataCacheService $deviceDataCacheService
    ) {
    }

    #[Route('/device/topics')]
    public function topics(Request $request): Response
    {
        $topics = $this->service->transcribeUri($request->get('topics'));

        if ($topics === []) {
            return new JsonResponse([
                'error' => 'empty topics'
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($this->deviceDataCacheService->getPayloadByTopicList($topics));
    }
}