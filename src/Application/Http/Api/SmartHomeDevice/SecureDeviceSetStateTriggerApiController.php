<?php

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceSetStateTriggerPresenter;
use App\Domain\DeviceData\Service\SecureDeviceDataService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecureDeviceSetStateTriggerApiController
{
    public function __construct(private readonly SecureDeviceDataService $service)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/secure/trigger')]
    public function setTrigger(Request $request): Response
    {
        $trigger = $request->get('trigger');
        $topic = $request->get('topic');

        if ($trigger === null) {
            return new JsonResponse([
                'error' => 'empty topics'
            ], 400);
        }

        $isTriggered = $trigger === 'true';

        $this->service->setTrigger($topic, $isTriggered);

        return new JsonResponse((new SecureDeviceSetStateTriggerPresenter($topic, $isTriggered))->present());
    }
}