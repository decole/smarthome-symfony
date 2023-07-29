<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Auth;

use App\Domain\Identity\Entity\User;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class TwoFactorController extends AbstractController
{
    public function __construct(private readonly TwoFactorService $service)
    {
    }

    #[Route('/2fa', name: '2fa')]
    public function index(Request $request, Security $security): Response
    {
        /** @var User $user */
        $user = $security->getUser();

        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('post')) {
            $code = $request->get('two-factor');

            $result = $this->service->checkCode($user, $code, $request);

            if (!$result->isCorrect) {
                return $this->renderPage($result->error);
            }

            return $this->redirectToRoute('home');
        }

        if ($this->service->isConfirm($user, $request)) {
            return $this->redirectToRoute('home');
        }

        return $this->renderPage();
    }

    private function renderPage(?string $error = null): Response
    {
        return $this->render('twofactor/index.html.twig', [
            'error' => $error,
            'host' => $this->getParameter('app.host'),
        ]);
    }
}