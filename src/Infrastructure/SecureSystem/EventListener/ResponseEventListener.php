<?php

declare(strict_types=1);

namespace App\Infrastructure\SecureSystem\EventListener;

use App\Domain\Identity\Entity\User;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;

#[AsEventListener(event: 'kernel.response', priority: 590)]
class ResponseEventListener
{
    private const ROUTE = '2fa';
    private const URI_TWO_FACTOR = '/2fa';

    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly ContainerInterface $container,
        private Security $security,
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