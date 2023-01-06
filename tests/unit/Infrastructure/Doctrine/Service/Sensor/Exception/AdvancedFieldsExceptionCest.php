<?php

namespace App\Tests\unit\Infrastructure\Doctrine\Service\Sensor\Exception;

use App\Infrastructure\Doctrine\Service\Sensor\Exception\AdvancedFieldsException;
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