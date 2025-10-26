<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\Presenter\SmartHomeDevice;

use App\Application\Presenter\Api\SmartHomeDevice\SecureDeviceSetStateTriggerPresenter;
use App\Tests\UnitTester;
use Codeception\Attribute\Skip;

#[Skip('This test not support new version codeception')]
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
