<?php


namespace App\Application\Http\Web\Auth;


use Symfony\Component\Routing\Annotation\Route;

class LogoutController
{
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}