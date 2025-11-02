<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Auth;

use App\Domain\Identity\Entity\User;
use App\Infrastructure\Output\Service\RateLimitService;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class SignInController extends AbstractController
{
    use TargetPathTrait;

    #[Route('/login', name: 'security_login')]
    public function index(
        AuthenticationUtils $authenticationUtils,
        RateLimitService $rateLimitService,
        TwoFactorService $service,
        #[CurrentUser]
        ?User $user,
        Request $request,
    ): Response {
        if ($user instanceof User) {
            return $this->redirectToRoute('main');
        }
        $rateLimitService->http($request);

        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('home_page'));

        return $this->render('login/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'two_factor' => false, // $service->isEnabled(),
            'host' => $this->getParameter('app.host'),
        ]);
    }
}
