<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\SecureSystem\EventListener;

use App\Tests\Support\Step\UnitStep\Infrastructure\TwoFactor\TwoFactorServiceStep;
use Codeception\Attribute\Skip;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseEventListenerCest
{
    #[Skip('This test not support new version codeception')]
    public function isVerifiedSession(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setTwoFactorSecret($I->faker()->word());
        $event = $I->createEvent($user, true);
        $listener = $I->createListener($user);
        $listener($event);
        $response = $event->getResponse();

        $I->assertEquals(Response::class, $response::class);
    }

    #[Skip('This test not support new version codeception')]
    public function isNotVerifedSession(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $event = $I->createEvent($user, false);
        $listener = $I->createListener($user);
        $listener($event);
        $response = $event->getResponse();

        $I->assertEquals(RedirectResponse::class, $response::class);
    }

    #[Skip('This test not support new version codeception')]
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
