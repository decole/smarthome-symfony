<?php

namespace App\Application\Http\Api\YandexSkill;

use App\Infrastructure\AliceSkill\Service\AliceSkillService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class YandexSkillApiController
{
    public function __construct(private AliceSkillService $service)
    {
    }

    #[Route('/alice')]
    public function index(): Response
    {
        $content = file_get_contents('php://input');
        $request = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $answer = $this->service->getDialogAnswer($request);

        return new JsonResponse($answer->getResult());
    }
}