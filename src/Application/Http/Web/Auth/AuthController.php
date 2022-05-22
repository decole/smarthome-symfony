<?php

namespace App\Application\Http\Web\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/auth', name: 'auth_authorization')]
    final public function registration(): Response
    {
        // todo login by request data
        return $this->render('auth/login.html.twig', [
            'csrf' => ''
        ]);
    }
}