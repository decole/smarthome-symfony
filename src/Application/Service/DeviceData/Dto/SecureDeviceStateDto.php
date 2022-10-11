<?php

namespace App\Application\Service\DeviceData\Dto;

class SecureDeviceStateDto
{
    // true - задетектировано движение / false - состояние датчика не изменено
    public bool $standardisedState = false;
    // взведен ли ланный датчик на охрану системы
    public bool $isGuarded = false;
}