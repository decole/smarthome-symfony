<?php

declare(strict_types=1);

namespace App\Application\Http\Api\YandexSkill;

use App\Infrastructure\AliceSkill\Service\AliceSkillService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class YandexSkillApiController extends AbstractFOSRestController
{
    public function __construct(private readonly AliceSkillService $service) {}

    #[Route('/alice')]
    public function index(): Response
    {
        $content = file_get_contents('php://input');
        $request = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        $answer = $this->service->getDialogAnswer($request);

        return new JsonResponse($answer->getResult());
    }
}
