<?php

namespace App\Tests\_support\Step\Api;

use App\Tests\ApiTester;

class DeviceDataStep extends ApiTester
{
    public function deviceTopicsList(mixed $deviceTopicList): void
    {
        $this->sendGet('/device/topics', [
            'topics' => $deviceTopicList,
        ]);
    }
}