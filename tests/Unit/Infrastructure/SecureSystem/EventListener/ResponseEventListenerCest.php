<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\SecureSystem\EventListener;

use App\Tests\_support\Step\UnitStep\Infrastructure\TwoFactor\TwoFactorServiceStep;
use Codeception\Attribute\Skip;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

#[Skip('This test not support new version codeception')]
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

        $I->assertEquals(Response::class, $response::class);
    }

    public function isNotVerifedSession(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $event = $I->createEvent($user, false);
        $listener = $I->createListener($user);
        $listener($event);
        $response = $event->getResponse();

        $I->assertEquals(RedirectResponse::class, $response::class);
    }

    public function noAuth(TwoFactorServiceStep $I): void
    {
        $I->getUser();
        $event = $I->createEvent();
        $listener = $I->createListener();
        $listener($event);
        $response = $event->getResponse();

        $I->assertEquals(Response::class, $response::class);
    }
}
