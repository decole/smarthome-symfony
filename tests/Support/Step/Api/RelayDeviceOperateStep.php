<?php

declare(strict_types=1);

namespace App\Tests\Support\Step\Api;

use App\Tests\Support\ApiTester;

class RelayDeviceOperateStep extends ApiTester
{
    public function sendToRelay(array $data): void
    {
        $this->sendPost('/device/send', $data);
    }
}
