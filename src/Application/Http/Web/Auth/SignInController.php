<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Auth;

use App\Infrastructure\Output\Service\RateLimitService;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SignInController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(
        AuthenticationUtils $authenticationUtils,
        RateLimitService $rateLimitService,
        TwoFactorService $service,
        Request $request
    ): Response {
        $rateLimitService->http($request);

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastEmail,
            'error' => $error,
            'two_factor' => $service->isEnabled(),
            // see /config/services.yaml app.registration parameter
            'isEnableRegistration' => $this->getParameter('app.registration'),
            'host' => $this->getParameter('app.host'),
        ]);
    }
}