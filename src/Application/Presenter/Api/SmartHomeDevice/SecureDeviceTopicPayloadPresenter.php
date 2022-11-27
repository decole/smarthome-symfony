<?php

namespace App\Application\Presenter\Api\SmartHomeDevice;

use App\Application\Presenter\Api\PresenterInterface;
use App\Domain\DeviceData\Entity\SecureDeviceDataState;

final class SecureDeviceTopicPayloadPresenter implements PresenterInterface
{
    public function __construct(private SecureDeviceDataState $dto)
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