<?php

namespace App\Domain\SecureSystem\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

#[AsEventListener(priority: 590)]
class AuthenticationSuccessEventListener
{
    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $token = $event->getAuthenticationToken();

        $user = $token->getUser();

        // todo проверить сессию на прохождение второго фактора, если он включен и настроен
    }
}