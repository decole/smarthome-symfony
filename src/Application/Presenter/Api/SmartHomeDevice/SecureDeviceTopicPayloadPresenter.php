<?php

namespace App\Application\Presenter\Api\SmartHomeDevice;

use App\Application\Presenter\Api\PresenterInterface;
use App\Application\Service\DeviceData\Dto\SecureDeviceStateDto;

final class SecureDeviceTopicPayloadPresenter implements PresenterInterface
{
    public function __construct(private SecureDeviceStateDto $dto)
    {
    }

    public function present(): array
    {
        return [
            'state' => $this->dto->standardisedState,
            'isTriggered' => $this->dto->isGuarded,
        ];
    }
}