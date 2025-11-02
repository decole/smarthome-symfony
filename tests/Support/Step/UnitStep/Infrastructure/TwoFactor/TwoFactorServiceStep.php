<?php

declare(strict_types=1);

namespace App\Tests\Support\Step\UnitStep\Infrastructure\TwoFactor;

use App\Domain\Identity\Entity\User;
use App\Infrastructure\SecureSystem\EventListener\ResponseEventListener;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use App\Tests\Support\UnitTester;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;

final class TwoFactorServiceStep extends UnitTester
{
    public function createEvent(?User $user = null, bool $isVerified = false): ResponseEvent
    {
        $kernel = $this->grabService(HttpKernelInterface::class);
        $requestType = HttpKernelInterface::MAIN_REQUEST;
        $response = new Response();
        $request = new Request();
        $session = $this->grabService(SessionInterface::class);
        $request->setSession($session);

        if ($user && $isVerified) {
            (new TwoFactorService(true))->setSessionIsVerifiedState($user, $request);
        }

        return new ResponseEvent($kernel, $request, $requestType, $response);
    }

    public function createListener(?User $user = null): ResponseEventListener
    {
        $service = new TwoFactorService(true);

        return new ResponseEventListener(
            twoFactorService: $service,
            router: $this->grabService(RouterInterface::class),
            security: $this->grabService(Security::class),
        );
    }

    public function getRequest(): Request
    {
        $request = new Request();
        $session = $this->grabService(SessionInterface::class);
        $request->setSession($session);

        return $request;
    }
}
