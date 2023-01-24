<?php

namespace App\Application\Http\Web\Auth;

use App\Infrastructure\Security\Auth\Service\CsrfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SignUpController extends AbstractController
{
    public function __construct(private readonly CsrfService $csrf)
    {
    }

    #[Route('/signup', name: 'app_signup')]
    final public function registration(): Response
    {
        return $this->render('registration/index.html.twig', [
            'csrf' => $this->csrf->getToken(true),
            'name' => '',
            'email' => '',
            'errors' => null,
            'isEnableRegistration' => $this->getParameter('app.registration'),
            'host' => $this->getParameter('app.host'),
        ]);
    }
}