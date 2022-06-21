<?php

namespace App\Tests\functional;

use App\Tests\UnitTester;

class FunctionalTestServiceCest
{
    public function withOnlyDateEnd(UnitTester $I): void
    {
        $dateEnd = new \DateTimeImmutable();

        $I->assertEquals($dateEnd->format(DATE_ATOM), $dateEnd->format(DATE_ATOM));
    }
}