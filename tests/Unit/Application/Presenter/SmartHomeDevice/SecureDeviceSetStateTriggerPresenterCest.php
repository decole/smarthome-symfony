<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Presenter\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceSetStateTriggerPresenter;
use App\Tests\Support\UnitTester;

class SecureDeviceSetStateTriggerPresenterCest
{
    public function positivePresent(UnitTester $I): void
    {
        $topic = $I->faker()->word();
        $trigger = true;

        $presenter = new SecureDeviceSetStateTriggerPresenter($topic, $trigger);

        $I->assertEquals(
            [
                'topic' => $topic,
                'trigger' => $trigger,
            ],
            $presenter->present(),
        );
    }
}
