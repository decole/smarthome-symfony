<?php

declare(strict_types=1);

namespace App\Domain\Common\Enum;

enum EntityStatusEnum: int
{
    case STATUS_WARNING = 2;
    case STATUS_ACTIVE = 1;
    case STATUS_DEACTIVATE = 0;
}