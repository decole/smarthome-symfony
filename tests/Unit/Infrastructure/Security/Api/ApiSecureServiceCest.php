<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security\Api;

use App\Infrastructure\Security\Api\ApiSecureService;
use App\Tests\Support\UnitTester;

class ApiSecureServiceCest
{
    public function positiveValidate(UnitTester $I): void
    {
        $targetToken = $I->faker()->word();
        $service = new ApiSecureService($targetToken);

        $I->assertEquals(true, $service->validate($targetToken));
    }

    public function negativeValidate(UnitTester $I): void
    {
        $targetToken = $I->faker()->word();
        $wrongToken = $targetToken . '_';
        $service = new ApiSecureService($targetToken);

        $I->assertEquals(false, $service->validate($wrongToken));
    }
}
