<?php

declare(strict_types=1);

namespace App\Tests\Support\Step\Api;

use App\Tests\Support\ApiTester;

class DeviceDataStep extends ApiTester
{
    public function deviceTopicsList(mixed $deviceTopicList): void
    {
        $this->sendGet('/device/topics', [
            'topics' => $deviceTopicList,
        ]);
    }
}
