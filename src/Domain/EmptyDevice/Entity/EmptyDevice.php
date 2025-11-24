<?php

declare(strict_types=1);

namespace App\Domain\EmptyDevice\Entity;

use App\Domain\Contract\Repository\EntityInterface;

class EmptyDevice implements EntityInterface
{
    public static function alias(): string
    {
        return 'empty';
    }
}
