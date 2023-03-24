<?php

namespace App\Domain\Relay\Enum;

enum RelayTypeEnum: string
{
    case DRY_RELAY_TYPE = 'relay';
    case WATERING_SWIFT_TYPE = 'swift';
}