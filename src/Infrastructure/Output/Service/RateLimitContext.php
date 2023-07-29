<?php

declare(strict_types=1);

namespace App\Infrastructure\Output\Service;

class RateLimitContext
{
    public function __construct(
        public readonly string $ip,
        public string $cacheKey,
        public readonly int $limit,
        public readonly int $minutes
    ) {
    }
}