<?php

namespace App\Tests\_support\Step\UnitStep\Infrastructure\TwoFactor;

use App\Domain\Identity\Entity\User;
use App\Infrastructure\SecureSystem\EventListener\ResponseEventListener;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use App\Tests\UnitTester;
use Codeception\Stub;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Security;

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
        $container = $this->grabService(ContainerInterface::class);
        $security = Stub::makeEmpty(Security::class, [
            'getUser' => $user,
        ]);

        return new ResponseEventListener(
            twoFactorService: $service,
            container: $container,
            security: $security
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