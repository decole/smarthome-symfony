<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\TwoFactor\Service;

use App\Infrastructure\TwoFactor\Service\TwoFactorQrCodeService;
use App\Tests\UnitTester;
use Codeception\Attribute\Skip;

#[Skip('This test not support new version codeception')]
class TwoFactorQrCodeServiceCest
{
    public function positiveGenerateImageSource(UnitTester $I): void
    {
        $user = $I->getUser();
        $secret = $I->faker()->word();
        $service = new TwoFactorQrCodeService($I->faker()->word());

        $I->assertNotEmpty($service->generateImageSource($user, $secret));
    }

    public function negativeGenerateImageSource(UnitTester $I): void
    {
        $I->expectThrowable(\Throwable::class, fn() => new TwoFactorQrCodeService());
    }
}
