<?php

declare(strict_types=1);

namespace App\Tests\Api\SmartHomeDevice;

use App\Tests\Support\Step\Api\RelayDeviceOperateStep;

class RelayDeviceOperateApiControllerCest
{
    public function positivePostTopicWithPayload(RelayDeviceOperateStep $I): void
    {
        $data = [
            'topic' => $I->faker()->word(),
            'payload' => $I->faker()->word(),
        ];
        $I->sendToRelay($data);
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            'status' => 'success',
        ]);
    }

    public function negativePostTopicWithPayload(RelayDeviceOperateStep $I): void
    {
        $data = [];
        $I->sendToRelay($data);
        $I->seeResponseIsException();
        $I->seeResponseContainsJson([
            'error' => 'empty post data',
        ]);
    }
}
