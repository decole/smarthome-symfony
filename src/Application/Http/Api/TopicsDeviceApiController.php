<?php

namespace App\Application\Http\Api;

use App\Application\Service\DeviceData\DeviceDataCacheService;
use App\Application\Service\SitePage\ApiUriTranscribeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TopicsDeviceApiController
{
    public function __construct(private ApiUriTranscribeService $service, private DeviceDataCacheService $deviceDataCacheService)
    {
    }

    #[Route('/device/topics')]
    public function topics(Request $request): Response
    {
        $topics = $this->service->transcribeUri($request->get('topics'));

        if (empty($topics)) {
            return new JsonResponse([
                'error' => 'empty topics'
            ], 400);
        }

        return new JsonResponse($this->deviceDataCacheService->getPayloadByTopicList($topics));
    }
}
