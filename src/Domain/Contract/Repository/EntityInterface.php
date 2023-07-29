<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

interface EntityInterface
{
    public static function alias(): string;
}