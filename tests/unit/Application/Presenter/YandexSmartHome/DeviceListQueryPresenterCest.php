<?php

namespace App\Tests\unit\Application\Presenter\YandexSmartHome;

use App\Application\Presenter\Api\YandexSmartHome\DeviceListQueryPresenter;
use App\Tests\UnitTester;

class DeviceListQueryPresenterCest
{
    public function positivePresent(UnitTester $I): void
    {
        $devices = [];
        $requestId = $I->faker()->word();

        $presenter = new DeviceListQueryPresenter($devices, $requestId);

        $I->assertEquals($requestId, $presenter->present()['request_id']);
    }
}