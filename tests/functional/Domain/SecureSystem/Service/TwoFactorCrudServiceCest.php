<?php

namespace App\Tests\functional\Domain\SecureSystem\Service;

use App\Tests\_support\Step\FunctionalStep\Domain\SecureSystem\TwoFactorCrudServiceStep;

class TwoFactorCrudServiceCest
{
    public function positiveAdd(TwoFactorCrudServiceStep $I): void
    {
        $user = $I->getUser();
        $secret = $I->faker()->word();
        $service = $I->getService($user);
        $service->add($user, $secret);

        $I->assertEquals($secret, $user->getTwoFactorCode());
    }

    public function positiveDeleteWithCode(TwoFactorCrudServiceStep $I): void
    {
        $user = $I->getUser();
        $secret = $I->faker()->word();
        $service = $I->getService($user);
        $service->add($user, $secret);

        $I->assertEquals($secret, $user->getTwoFactorCode());

        $service->delete($user, $I->getRequestWithSession());

        $I->assertEquals(null, $user->getTwoFactorCode());
    }

    public function positiveDeleteWithoutCode(TwoFactorCrudServiceStep $I): void
    {
        $user = $I->getUser();
        $service = $I->getService($user);
        $service->delete($user, $I->getRequestWithSession());

        $I->assertEquals(null, $user->getTwoFactorCode());
    }
}