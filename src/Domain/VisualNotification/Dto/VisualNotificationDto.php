<?php

declare(strict_types=1);

namespace App\Domain\VisualNotification\Dto;

final class VisualNotificationDto
{
    public function __construct(
        private readonly int $type,
        private readonly string $message
    ) {
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}