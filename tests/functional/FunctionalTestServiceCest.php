<?php

namespace App\Tests\functional;

use App\Tests\FunctionalTester;

class FunctionalTestServiceCest
{
    public function withOnlyDateEnd(FunctionalTester $I): void
    {
        $dateEnd = new \DateTimeImmutable();

        $I->assertEquals($dateEnd->format(DATE_ATOM), $dateEnd->format(DATE_ATOM));
    }
}