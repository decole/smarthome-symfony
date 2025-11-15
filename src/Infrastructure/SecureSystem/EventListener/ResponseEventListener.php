<?php

declare(strict_types=1);

namespace App\Infrastructure\SecureSystem\EventListener;

use App\Domain\Identity\Entity\User;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Routing\RouterInterface;

#[AsEventListener(event: 'kernel.response', priority: 590)]
class ResponseEventListener
{
    private const string ROUTE = '2fa';
    private const string URI_TWO_FACTOR = '/%s/2fa';

    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly RouterInterface $router,
        private readonly Security $security,
        #[Autowire('%app.locale%')]
        private readonly string $locale,
    ) {}

    public function __invoke(ResponseEvent $event): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        if ($user->isTwoFactorEnable()
            && !$this->twoFactorService->isConfirm($user, $event->getRequest())
            && sprintf(self::URI_TWO_FACTOR, $this->locale) !== $event->getRequest()->getRequestUri()
        ) {
            $event->setResponse(new RedirectResponse(
                $this->router->generate(self::ROUTE),
            ));
        }
    }
}
