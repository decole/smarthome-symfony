<?php

declare(strict_types=1);

namespace App\Domain\Contract\Service;

interface CacheServiceInterface
{
    public function create(): void;
}