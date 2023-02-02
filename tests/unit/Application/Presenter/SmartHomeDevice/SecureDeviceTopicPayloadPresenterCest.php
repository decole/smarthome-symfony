<?php

namespace App\Tests\unit\Application\Presenter\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceTopicPayloadPresenter;
use App\Domain\DeviceData\Entity\SecureDeviceDataState;
use App\Tests\UnitTester;

class SecureDeviceTopicPayloadPresenterCest
{
    public function positivePresent(UnitTester $I): void
    {
        $dto = new SecureDeviceDataState();

        $presenter = new SecureDeviceTopicPayloadPresenter($dto);

        $I->assertEquals(
            [
                'state' => false,
                'isTriggered' => false,
            ],
            $presenter->present()
        );
    }

    public function positivePresentWithTriggeredState(UnitTester $I): void
    {
        $dto = new SecureDeviceDataState();
        $dto->isGuarded = true;
        $dto->standardisedState = true;

        $presenter = new SecureDeviceTopicPayloadPresenter($dto);

        $I->assertEquals(
            [
                'state' => true,
                'isTriggered' => true,
            ],
            $presenter->present()
        );
    }
}