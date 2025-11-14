<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\TwoFactor\Service;

use App\Infrastructure\TwoFactor\Service\TwoFactorQrCodeService;
use App\Tests\Support\UnitTester;
use Codeception\Attribute\Skip;

class TwoFactorQrCodeServiceCest
{
    #[Skip('This test not support new version codeception')]
    public function positiveGenerateImageSource(UnitTester $I): void
    {
        $user = $I->getUser();
        $secret = $I->faker()->word();
        $service = new TwoFactorQrCodeService($I->faker()->word());

        $I->assertNotEmpty($service->generateImageSource($user, $secret));
    }

    public function negativeGenerateImageSource(UnitTester $I): void
    {
        $I->expectThrowable(\Throwable::class, fn(): TwoFactorQrCodeService => new TwoFactorQrCodeService());
    }
}
