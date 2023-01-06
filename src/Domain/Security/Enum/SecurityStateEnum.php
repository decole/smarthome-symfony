<?php

namespace App\Domain\Security\Enum;

enum SecurityStateEnum: string
{
    case GUARD_STATE = 'guard';
    case HOLD_STATE = 'hold';
}
