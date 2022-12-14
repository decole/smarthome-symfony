<?php

namespace App\Tests\unit\Domain\Sensor\Exception;

use App\Domain\Sensor\Exception\AdvancedFieldsException;
use App\Tests\UnitTester;

class AdvancedFieldsExceptionCest
{
    public function positiveThrow(UnitTester $I): void
    {
        $type = $I->faker()->word();

        $I->assertInstanceOf(AdvancedFieldsException::class, AdvancedFieldsException::deviceTypeNotFound($type));
        $I->expectThrowable(
            new AdvancedFieldsException('Advanced fields for device type \'' . $type . '\' not found.'),
            fn() => throw AdvancedFieldsException::deviceTypeNotFound($type)
        );
    }
}