<?php

namespace App\Tests\functional\Application\Exception;

use App\Application\Exception\DeviceDataException;
use App\Tests\FunctionalTester;

class DeviceDataExceptionCest
{
    public function getNotFoundPageMessage(FunctionalTester $I): void
    {
        $entity = DeviceDataException::notFoundPageEntity($id = $I->faker()->word());

        $I->assertEquals("Not found page entity by id {$id}", $entity->getMessage());
    }

    public function getValidationErrorMessage(FunctionalTester $I): void
    {
        $validation = DeviceDataException::notFoundValidatorType();

        $I->assertEquals('Not found DeviceDataValidator by current device type', $validation->getMessage());
    }

    public function getTypeOnException(FunctionalTester $I): void
    {
        $type = DeviceDataException::getType();
        $I->assertEquals('DeviceDataException', $type);
    }
}