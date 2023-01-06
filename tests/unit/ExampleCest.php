<?php
namespace App\Tests;

use League\FactoryMuffin\Faker\Facade as Faker;

class ExampleCest
{
    public function testSomeFeature(UnitTester $I)
    {
        $t = Faker::email();

        $I->assertEquals(1,1);
        $I->assertEquals($t, $t);
    }
}