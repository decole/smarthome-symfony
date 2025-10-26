<?php

declare(strict_types=1);

namespace App\Application\Http\Api\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceTopicPayloadPresenter;
use App\Domain\DeviceData\Service\SecureDeviceDataService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecureDeviceTopicPayloadApiController extends AbstractFOSRestController
{
    public function __construct(private readonly SecureDeviceDataService $service) {}

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/secure/state')]
    public function secureTopicState(Request $request): Response
    {
        $topic = $request->get('topic');

        if (0 === mb_strlen($topic)) {
            return new JsonResponse([
                'error' => 'empty topics',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            (new SecureDeviceTopicPayloadPresenter($this->service->getDeviceState($topic)))->present(),
        );
    }
}
