<?php

namespace App\Tests\api\SmartHomeDevice;

use App\Tests\_support\Step\Api\SecureDeviceDataStep;

class SecureDeviceSetStateTriggerApiControllerCest
{
    public function positiveSetTriggerOn(SecureDeviceDataStep $I): void
    {
        $I->secureSetTrigger();
    }

    public function negativeSetTriggerOn(SecureDeviceDataStep $I): void
    {

    }

    public function positiveSetTriggerOff(SecureDeviceDataStep $I): void
    {

    }

    public function negativeSetTriggerOff(SecureDeviceDataStep $I): void
    {

    }
}