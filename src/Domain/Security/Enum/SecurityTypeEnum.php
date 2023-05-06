<?php

namespace App\Domain\Security\Enum;

enum SecurityTypeEnum: string
{
    case MQTT_TYPE = 'mqtt_security_device';
    case API_TYPE = 'api_security_device';
}
