<?php

declare(strict_types=1);

namespace App\Domain\DeviceData\Entity;

class SecureDeviceDataState
{
    // true - задетектировано движение / false - состояние датчика не изменено
    public bool $standardisedState = false;

    // взведен ли ланный датчик на охрану системы
    public bool $isGuarded = false;
}