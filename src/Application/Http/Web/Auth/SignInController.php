<?php


namespace App\Application\Http\Web\Auth;


use App\Infrastructure\Security\Auth\Service\CsrfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    public function __construct(private CsrfService $csrf)
    {
    }

    #[Route('/login', name: 'auth_login')]
    final public function registration(): Response
    {
        return $this->render('auth/login.html.twig', [
            'csrf' => $this->csrf->getToken(true),
        ]);
    }
}