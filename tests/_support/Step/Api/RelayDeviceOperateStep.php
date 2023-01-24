<?php

namespace App\Tests\_support\Step\Api;

use App\Tests\ApiTester;

class RelayDeviceOperateStep extends ApiTester
{
    public function sendToRelay(array $data): void
    {
        $this->sendPost('/device/send', $data);
    }
}