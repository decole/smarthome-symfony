<?php

declare(strict_types=1);

namespace App\Domain\VisualNotification\Dto;

final class VisualNotificationResultDto
{
    public function __construct(
        public readonly array $collection,
        public readonly int $count,
        public readonly int $prev,
        public readonly int $next,
        public readonly int $current
    ) {
    }
}