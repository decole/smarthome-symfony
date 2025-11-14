<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\SecureSystem\Service;

use App\Tests\_support\Step\FunctionalStep\Domain\SecureSystem\TwoFactorCrudServiceStep;
use Codeception\Attribute\Skip;

#[Skip('This test not support new version codeception')]
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
