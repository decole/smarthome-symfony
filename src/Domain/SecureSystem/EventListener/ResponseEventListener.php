<?php

namespace App\Domain\SecureSystem\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;
use Psr\Container\ContainerInterface;
use App\Domain\Identity\Entity\User;

#[AsEventListener(event: 'kernel.response', priority: 590)]
class ResponseEventListener
{
    private const ROUTE = '2fa';
    private const URI_TWO_FACTOR = '/2fa';

    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly ContainerInterface $container,
        private readonly Security $security,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        if ($this->twoFactorService->isEnabled() &&
            !$this->twoFactorService->isConfirm($user, $event->getRequest()) &&
            $event->getRequest()->getRequestUri() !== self::URI_TWO_FACTOR
        ) {
            $event->setResponse(new RedirectResponse($this->container->get('router')->generate(self::ROUTE)));
        }
    }
}