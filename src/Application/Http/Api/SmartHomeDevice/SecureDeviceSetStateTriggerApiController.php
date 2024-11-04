<?php

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceSetStateTriggerPresenter;
use App\Domain\DeviceData\Service\SecureDeviceDataService;
use App\Infrastructure\Security\Api\ApiSecureService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecureDeviceSetStateTriggerApiController
{
    public function __construct(
        private readonly SecureDeviceDataService $service,
        private readonly ApiSecureService $apiSecureService
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/secure/trigger')]
    public function setTrigger(Request $request): Response
    {
        $trigger = $request->get('trigger');
        $topic = $request->get('topic');
        $secureToken = $request->request->get('token');

        if (mb_strlen($trigger) == 0 || mb_strlen($topic) == 0) {
            return new JsonResponse([
                'error' => 'empty topic or trigger state'
            ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

        $isTriggered = $trigger === 'true';

        if ($this->apiSecureService->validate($secureToken)) {
            $this->service->setTrigger($topic, $isTriggered);
        }

        return new JsonResponse((new SecureDeviceSetStateTriggerPresenter($topic, $isTriggered))->present());
    }
}