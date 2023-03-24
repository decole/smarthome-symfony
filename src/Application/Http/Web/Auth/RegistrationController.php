<?php

namespace App\Application\Http\Web\Auth;

use App\Domain\SecureSystem\Dto\RegisterDto;
use App\Domain\SecureSystem\Service\RegistrationValidateService;
use App\Infrastructure\Security\Auth\Service\CsrfService;
use App\Infrastructure\Security\Register\Service\SignUpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly CsrfService $csrf,
        private readonly RegistrationValidateService $validation,
        private readonly SignUpService $service
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $dto = new RegisterDto($request);

        [$valid, $errors] = $this->validation->validate($dto, $this->csrf->getToken());

        if (!$valid) {
            $this->addFlash('errors', $errors);

            return $this->redirectToRoute('app_signup');
        }

        $this->service->signUp($dto);

        return $this->redirectToRoute('home');
    }
}