<?php

namespace App\Domain\Common\Enum;

enum EntityStatusEnum: int
{
    case STATUS_WARNING = 2;
    case STATUS_ACTIVE = 1;
    case STATUS_DEACTIVATE = 0;
}