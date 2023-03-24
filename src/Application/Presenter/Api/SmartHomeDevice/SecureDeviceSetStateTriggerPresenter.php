<?php

namespace App\Application\Presenter\Api\SmartHomeDevice;

use App\Application\Presenter\Api\PresenterInterface;

final class SecureDeviceSetStateTriggerPresenter implements PresenterInterface
{
    public function __construct(private string $topic, private bool $trigger)
    {
    }

    public function present(): array
    {
        return [
            'topic' => $this->topic,
            'trigger' => $this->trigger,
        ];
    }
}