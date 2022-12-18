<?php

namespace App\Tests\unit\Application\Helper;

use App\Application\Helper\StringHelper;
use App\Tests\UnitTester;

class StringHelperCest
{
    public function checkSanitize(UnitTester $I): void
    {
        $I->assertEquals(null, StringHelper::sanitize(null));
        $I->assertEquals('default', StringHelper::sanitize(null, 'default'));
        $I->assertEquals('example', StringHelper::sanitize('example'));
        $I->assertEquals('&lt;p&gt;example&lt;/p&gt;', StringHelper::sanitize('<p>example</p>'));
    }

    public function checkCleanReservedCharacters(UnitTester $I): void
    {
        $I->assertEquals(null, StringHelper::cleanReservedCharacters(null));
        $I->assertEquals($one = $I->faker()->word(), StringHelper::cleanReservedCharacters($one));
        $I->assertEquals('__1__2_3_4_5_', StringHelper::cleanReservedCharacters('{}1()2/3\\4@5:'));
    }
}