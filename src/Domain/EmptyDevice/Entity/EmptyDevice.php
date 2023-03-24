<?php

namespace App\Domain\EmptyDevice\Entity;

use App\Domain\Contract\Repository\EntityInterface;

final class EmptyDevice implements EntityInterface
{
    public static function alias(): string
    {
        return 'empty';
    }
}