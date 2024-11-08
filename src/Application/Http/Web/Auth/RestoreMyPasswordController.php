<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Auth;

use App\Domain\SecureSystem\Service\RestorePasswordService;
use App\Infrastructure\Output\Service\RateLimitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RestoreMyPasswordController extends AbstractController
{
    public function __construct(
        private readonly RestorePasswordService $service,
        private readonly RateLimitService $rateLimitService
    ) {
    }

    #[Route(path: '/restore', name: 'app_restore_password')]
    public function restore(Request $request): Response
    {
        $this->rateLimitService->http($request);

        if ($token = $request->get('token')) {
            [$error, $status] = $this->service->restoreByToken($token);
        }

        return $this->render('login/restore.html.twig', [
            'isEnableRegistration' => $this->getParameter('app.registration'),
            'host' => $this->getParameter('app.host'),
            'error' => $error ?? null,
            'status' => $status ?? null,
        ]);
    }
}