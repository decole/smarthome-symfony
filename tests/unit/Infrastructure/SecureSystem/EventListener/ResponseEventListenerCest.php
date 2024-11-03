<?php

namespace App\Tests\unit\Infrastructure\SecureSystem\EventListener;

use App\Tests\_support\Step\UnitStep\Infrastructure\TwoFactor\TwoFactorServiceStep;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseEventListenerCest
{
    public function isVerifiedSession(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setAuthSecret($I->faker()->word());
        $event = $I->createEvent($user, true);
        $listener = $I->createListener($user);
        $listener($event);
        $response = $event->getResponse();

        $I->assertEquals(Response::class, get_class($response));
    }

    public function isNotVerifedSession(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $event = $I->createEvent($user, false);
        $listener = $I->createListener($user);
        $listener($event);
        $response = $event->getResponse();

        $I->assertEquals(RedirectResponse::class, get_class($response));
    }

    public function noAuth(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $event = $I->createEvent();
        $listener = $I->createListener();
        $listener($event);
        $response = $event->getResponse();

        $I->assertEquals(Response::class, get_class($response));
    }
}