<?php

declare(strict_types=1);

namespace App\Application\Http\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractFOSRestController
{
    #[Route('/', name: 'api_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
