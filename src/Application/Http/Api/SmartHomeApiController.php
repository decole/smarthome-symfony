<?php

namespace App\Application\Http\Api;

use App\Application\Presenter\Api\DeviceListQueryPresenter;
use App\Infrastructure\YandexSmartHome\Service\SmartHomeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

final class SmartHomeApiController
{
    public function __construct(private SmartHomeService $service, private LoggerInterface $logger)
    {
    }

    // Проверка доступности Endpoint URL провайдера
    #[Route('/alice_home/v1.0')]
    public function index(): Response
    {
        return new JsonResponse(['success' => true]);
    }

    // Оповещение о разъединении аккаунтов
    #[Route('/alice_home/v1.0/user/unlink')]
    public function unlink(Request $request): Response
    {
        $requestId = $this->service->getRequestId($request);

        return new JsonResponse(['request_id' => $requestId]);
    }

    // Информация об устройствах пользователя
    #[Route('/alice_home/v1.0/user/devices')]
    public function devices(Request $request): Response
    {
        $requestId = $this->service->getRequestId($request);

        $result = [
            'request_id' => $requestId,
            'payload' => [
                'user_id' => 'decole2014',
                'devices' => [
                    [
                        'id' =>  'switcher1',
                        'name' =>  'switcher1',
                        'type' =>  'devices.types.switch',
                        'capabilities' => [
                            [
                                'type' => 'devices.capabilities.on_off',
                                'retrievable' => true
                            ]
                        ],
                    ],
                ]
            ]
        ];

        return new JsonResponse($result);
    }

    // Информация о состояниях устройств пользователя
    #[Route('/alice_home/v1.0/user/devices/query')]
    public function query(Request $request): Response
    {
        $requestId = $this->service->getRequestId($request);
        $devices = $this->service->devicesQuery($request->getContent());

        $presenter = new DeviceListQueryPresenter($devices, $requestId);

        return new JsonResponse($presenter->present());
    }

    // Изменение состояния у устройств
    #[Route('/alice_home/v1.0/user/devices/action')]
    public function action(Request $request): Response
    {
        $content = file_get_contents('php://input');

        $this->logger->info('/alice_home/v1.0/user/devices/action', [
            'json' => $content,
        ]);

        $query = json_decode($content);

        $requestId = $this->service->getRequestId($request);

        $topic = 'margulis/lamp01';

        $state = $this->service->relayAction($topic, $query);

        $result = [
            "request_id" => $requestId,
            "payload" => [
                "user_id" => "decole2014",
                "devices" => [
                    [
                        "id" =>  '1',
                        "name" =>  'switcher1',
                        "type" =>  'devices.types.switch',
                        "capabilities" => [
                            [
                                "type" => "devices.capabilities.on_off",
                                "retrievable" => true,
                                "state" => [
                                    'instance' => 'on',
                                    "value" => $state,
                                    "action_result" => [
                                        "status" => "DONE"
                                    ],
                                ],
                            ]
                        ],
                    ]
                ]
            ]
        ];

        return new JsonResponse($result);
    }
}